import express from "express";
import { createServer as createViteServer } from "vite";
import path from "path";
import bcrypt from "bcryptjs";
import jwt from "jsonwebtoken";
import db from "./src/lib/db.js";

const JWT_SECRET = process.env.JWT_SECRET || "inventory-secret-key";

async function startServer() {
  const app = express();
  const PORT = process.env.PORT || 3000;

  app.use(express.json());

  // --- AUTH ROUTES ---
  app.post("/api/auth/register", (req, res) => {
    const { name, email, password } = req.body;
    try {
      const hashedPassword = bcrypt.hashSync(password, 10);
      const stmt = db.prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      const info = stmt.run(name, email, hashedPassword);
      res.json({ success: true, userId: info.lastInsertRowid });
    } catch (error) {
      res.status(400).json({ error: error.message });
    }
  });

  app.post("/api/auth/login", (req, res) => {
    const { email, password } = req.body;
    try {
      const user = db.prepare("SELECT * FROM users WHERE email = ?").get(email);
      if (!user || !bcrypt.compareSync(password, user.password)) {
        return res.status(401).json({ error: "Invalid credentials" });
      }
      const token = jwt.sign({ id: user.id, email: user.email, name: user.name }, JWT_SECRET, { expiresIn: "1d" });
      res.json({ token, user: { id: user.id, email: user.email, name: user.name } });
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  // --- MIDDLEWARE ---
  const authenticate = (req, res, next) => {
    const token = req.headers.authorization?.split(" ")[1];
    if (!token) return res.status(401).json({ error: "Unauthorized" });
    try {
      req.user = jwt.verify(token, JWT_SECRET);
      next();
    } catch (e) {
      res.status(401).json({ error: "Invalid token" });
    }
  };

  // --- INVENTORY ROUTES ---
  app.get("/api/items", authenticate, (req, res) => {
    const items = db.prepare("SELECT * FROM items ORDER BY name").all();
    res.json(items);
  });

  app.post("/api/items", authenticate, (req, res) => {
    const { name, category_id, unit, stock, min_stock } = req.body;
    const stmt = db.prepare("INSERT INTO items (name, category_id, unit, stock, min_stock) VALUES (?, ?, ?, ?, ?)");
    const info = stmt.run(name, category_id, unit, stock, min_stock);
    
    db.prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)")
      .run((req).user.id, "TAMBAH_BARANG", `Menambahkan barang baru: ${name}`);
      
    res.json({ id: info.lastInsertRowid });
  });

  app.put("/api/items/:id", authenticate, (req, res) => {
    const { name, category_id, unit, stock, min_stock } = req.body;
    db.prepare("UPDATE items SET name = ?, category_id = ?, unit = ?, stock = ?, min_stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
      .run(name, category_id, unit, stock, min_stock, req.params.id);
    res.json({ success: true });
  });

  app.delete("/api/items/:id", authenticate, (req, res) => {
    db.prepare("DELETE FROM items WHERE id = ?").run(req.params.id);
    res.json({ success: true });
  });

  app.get("/api/categories", authenticate, (req, res) => {
    res.json(db.prepare("SELECT * FROM categories ORDER BY name").all());
  });

  app.post("/api/categories", authenticate, (req, res) => {
    const info = db.prepare("INSERT INTO categories (name) VALUES (?)").run(req.body.name);
    res.json({ id: info.lastInsertRowid });
  });

  app.put("/api/categories/:id", authenticate, (req, res) => {
    db.prepare("UPDATE categories SET name = ? WHERE id = ?").run(req.body.name, req.params.id);
    res.json({ success: true });
  });

  app.delete("/api/categories/:id", authenticate, (req, res) => {
    try {
      db.prepare("DELETE FROM categories WHERE id = ?").run(req.params.id);
      res.json({ success: true });
    } catch (error) {
      res.status(400).json({ error: "Cannot delete category that is in use" });
    }
  });

  app.get("/api/suppliers", authenticate, (req, res) => {
    res.json(db.prepare("SELECT * FROM suppliers ORDER BY name").all());
  });

  app.post("/api/suppliers", authenticate, (req, res) => {
    const { name, address, contact } = req.body;
    const info = db.prepare("INSERT INTO suppliers (name, address, contact) VALUES (?, ?, ?)").run(name, address, contact);
    res.json({ id: info.lastInsertRowid });
  });

  app.put("/api/suppliers/:id", authenticate, (req, res) => {
    const { name, address, contact } = req.body;
    db.prepare("UPDATE suppliers SET name = ?, address = ?, contact = ? WHERE id = ?").run(name, address, contact, req.params.id);
    res.json({ success: true });
  });

  app.delete("/api/suppliers/:id", authenticate, (req, res) => {
    db.prepare("DELETE FROM suppliers WHERE id = ?").run(req.params.id);
    res.json({ success: true });
  });

  app.post("/api/transactions", authenticate, (req, res) => {
    const { type, itemId, quantity, supplierId, destination, note } = req.body;
    const userId = (req).user.id;

    const transaction = db.transaction(() => {
      const item = db.prepare("SELECT name, stock FROM items WHERE id = ?").get(itemId);
      if (!item) throw new Error("Barang tidak ditemukan");
      
      let newStock = item.stock;

      if (type === 'IN') newStock += quantity;
      else if (type === 'OUT') {
        if (item.stock < quantity) throw new Error("Stok tidak mencukupi");
        newStock -= quantity;
      } else if (type === 'ADJUST') {
        newStock = quantity;
      }

      db.prepare("UPDATE items SET stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?").run(newStock, itemId);
      db.prepare("INSERT INTO transactions (item_id, type, quantity, supplier_id, destination, note, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
        .run(itemId, type, quantity, supplierId || null, destination || null, note || null, userId);
      
      const actionMap = { 'IN': 'STOK_MASUK', 'OUT': 'STOK_KELUAR', 'ADJUST': 'PENYESUAIAN_STOK' };
      const detailsMap = { 'IN': 'Barang masuk', 'OUT': 'Barang keluar', 'ADJUST': 'Penyesuaian stok' };
      
      db.prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)")
        .run(userId, actionMap[type], `${detailsMap[type]}: ${item.name} (${quantity})`);
    });

    try {
      transaction();
      res.json({ success: true });
    } catch (e) {
      res.status(400).json({ error: e.message });
    }
  });

  app.put("/api/transactions/:id", authenticate, (req, res) => {
    const { type, itemId, quantity, supplierId, destination, note } = req.body;
    db.prepare("UPDATE transactions SET type = ?, item_id = ?, quantity = ?, supplier_id = ?, destination = ?, note = ? WHERE id = ?")
      .run(type, itemId, quantity, supplierId || null, destination || null, note || null, req.params.id);
    res.json({ success: true });
  });

  app.delete("/api/transactions/:id", authenticate, (req, res) => {
    db.prepare("DELETE FROM transactions WHERE id = ?").run(req.params.id);
    res.json({ success: true });
  });

  app.get("/api/transactions", authenticate, (req, res) => {
    res.json(db.prepare(`
      SELECT t.*, i.name as item_name 
      FROM transactions t 
      JOIN items i ON t.item_id = i.id 
      ORDER BY t.date DESC
    `).all());
  });

  app.get("/api/logs", authenticate, (req, res) => {
    res.json(db.prepare(`
      SELECT l.*, u.name as user_name 
      FROM activity_logs l 
      JOIN users u ON l.user_id = u.id 
      ORDER BY l.timestamp DESC LIMIT 100
    `).all());
  });

  app.get("/api/stats", authenticate, (req, res) => {
    const totalItems = db.prepare("SELECT COUNT(*) as count FROM items").get();
    const lowStock = db.prepare("SELECT COUNT(*) as count FROM items WHERE stock <= min_stock").get();
    const outOfStock = db.prepare("SELECT COUNT(*) as count FROM items WHERE stock = 0").get();
    const items = db.prepare("SELECT * FROM items").all();
    res.json({
      totalItems: totalItems.count,
      lowStock: lowStock.count,
      outOfStock: outOfStock.count,
      items
    });
  });

  // --- VITE MIDDLEWARE ---
  if (process.env.NODE_ENV !== "production") {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.join(process.cwd(), 'dist');
    app.use(express.static(distPath));
    app.get('*', (req, res) => {
      res.sendFile(path.join(distPath, 'index.html'));
    });
  }

  app.listen(PORT, "0.0.0.0", () => {
    console.log(`Server running on http://localhost:${PORT}`);
  });
}

startServer();
