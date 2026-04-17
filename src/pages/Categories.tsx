import React, { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2 } from 'lucide-react';
import { Button } from '../components/ui/button';
import { Input } from '../components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../components/ui/dialog';
import { toast } from 'sonner';
import { inventoryService } from '../services/inventoryService';

export default function Categories() {
  const [categories, setCategories] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [catToDelete, setCatToDelete] = useState<number | null>(null);
  const [editingCat, setEditingCat] = useState<any>(null);
  const [name, setName] = useState('');

  useEffect(() => { fetchCats(); }, []);

  const fetchCats = async () => {
    setLoading(true);
    try {
      const data = await inventoryService.getCategories();
      setCategories(data);
    } catch (e) { toast.error("Gagal ambil data"); }
    finally { setLoading(false); }
  };

  const handleSave = async () => {
    if (!name) return;
    try {
      const url = editingCat ? `/api/categories/${editingCat.id}` : '/api/categories';
      const method = editingCat ? 'PUT' : 'POST';
      
      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({ name })
      });

      if (!res.ok) throw new Error("Failed to save");

      toast.success(editingCat ? "Kategori diperbarui" : "Kategori ditambahkan");
      setIsDialogOpen(false);
      setName('');
      setEditingCat(null);
      fetchCats();
    } catch (e) { toast.error("Gagal simpan"); }
  };

  const handleDelete = async () => {
    if (!catToDelete) return;
    try {
      const res = await fetch(`/api/categories/${catToDelete}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      if (!res.ok) {
        const err = await res.json();
        throw new Error(err.error || "Failed to delete");
      }
      toast.success("Kategori dihapus");
      setIsDeleteDialogOpen(false);
      setCatToDelete(null);
      fetchCats();
    } catch (e: any) { toast.error(e.message || "Gagal hapus"); }
  };

  return (
    <div className="space-y-6 animate-in fade-in duration-500">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 className="text-2xl font-bold text-slate-900">Kategori Barang</h1>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger
            render={
              <Button className="bg-[#6366f1] hover:bg-[#4f46e5] text-white shadow-lg shadow-indigo-500/20" onClick={() => { setEditingCat(null); setName(''); }}>
                <Plus size={18} className="mr-2" /> Tambah Kategori
              </Button>
            }
          />
          <DialogContent>
            <DialogHeader><DialogTitle>{editingCat ? 'Edit' : 'Tambah'} Kategori</DialogTitle></DialogHeader>
            <div className="py-4 space-y-2">
              <label className="text-sm font-medium">Nama Kategori</label>
              <Input value={name} onChange={e => setName(e.target.value)} placeholder="Contoh: Bahan Baku / Minuman" />
            </div>
            <DialogFooter>
              <Button variant="outline" onClick={() => setIsDialogOpen(false)}>Batal</Button>
              <Button onClick={handleSave} className="bg-[#6366f1] text-white">Simpan</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
      <div className="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
          <TableHeader><TableRow><TableHead>No</TableHead><TableHead>Nama Kategori</TableHead><TableHead className="text-right">Aksi</TableHead></TableRow></TableHeader>
          <TableBody>
            {categories.map((cat, i) => (
              <TableRow key={cat.id}>
                <TableCell>{i+1}</TableCell>
                <TableCell className="font-medium">{cat.name}</TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="icon" onClick={() => { setEditingCat(cat); setName(cat.name); setIsDialogOpen(true); }}>
                      <Edit2 size={16} />
                    </Button>
                    <Button variant="ghost" size="icon" className="text-red-500 hover:text-red-700" onClick={() => {
                      setCatToDelete(cat.id);
                      setIsDeleteDialogOpen(true);
                    }}>
                      <Trash2 size={16} />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>

      {/* Delete Confirmation Dialog */}
      <Dialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Konfirmasi Hapus</DialogTitle>
            <DialogDescription>
              Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsDeleteDialogOpen(false)}>Batal</Button>
            <Button variant="destructive" onClick={handleDelete} className="bg-red-600 hover:bg-red-700 text-white">Hapus</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
