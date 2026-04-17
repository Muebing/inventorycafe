import React, { useState, useEffect } from 'react';
import { 
  Plus, 
  Search, 
  Filter, 
  MoreVertical, 
  Edit2, 
  Trash2, 
  Package,
  RefreshCw,
  ChevronLeft,
  ChevronRight
} from 'lucide-react';
import { inventoryService } from '../services/inventoryService';
import { Button } from '../components/ui/button';
import { Input } from '../components/ui/input';
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from '../components/ui/table';
import { 
  Dialog, 
  DialogContent, 
  DialogDescription, 
  DialogFooter, 
  DialogHeader, 
  DialogTitle, 
  DialogTrigger 
} from '../components/ui/dialog';
import { 
  Select, 
  SelectContent, 
  SelectItem, 
  SelectTrigger, 
  SelectValue 
} from '../components/ui/select';
import { Badge } from '../components/ui/badge';
import { toast } from 'sonner';

export default function Items() {
  const [items, setItems] = useState<any[]>([]);
  const [categories, setCategories] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [filterCategory, setFilterCategory] = useState('all');
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [itemToDelete, setItemToDelete] = useState<string | null>(null);
  const [editingItem, setEditingItem] = useState<any>(null);

  // Form State
  const [formData, setFormData] = useState({
    name: '',
    category_id: '',
    unit: 'pcs',
    stock: 0,
    min_stock: 0
  });

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [itemsData, catsData] = await Promise.all([
        inventoryService.getItems(),
        inventoryService.getCategories()
      ]);
      setItems(itemsData);
      setCategories(catsData);
    } catch (error) {
      toast.error("Gagal mengambil data");
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    try {
      const url = editingItem ? `/api/items/${editingItem.id}` : '/api/items';
      const method = editingItem ? 'PUT' : 'POST';
      
      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(formData)
      });

      if (!res.ok) throw new Error("Failed to save");

      toast.success(editingItem ? "Barang diperbarui" : "Barang ditambahkan");
      setIsDialogOpen(false);
      setEditingItem(null);
      setFormData({ name: '', category_id: '', unit: 'pcs', stock: 0, min_stock: 0 });
      fetchData();
    } catch (error) {
      toast.error("Gagal menyimpan data");
    }
  };

  const handleDelete = async () => {
    if (!itemToDelete) return;
    try {
      const res = await fetch(`/api/items/${itemToDelete}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      if (!res.ok) throw new Error("Failed to delete");
      toast.success("Barang berhasil dihapus");
      setIsDeleteDialogOpen(false);
      setItemToDelete(null);
      fetchData();
    } catch (error) {
      toast.error("Gagal menghapus data");
    }
  };

  const filteredItems = items.filter(item => {
    const matchesSearch = item.name.toLowerCase().includes(search.toLowerCase());
    const matchesCategory = filterCategory === 'all' || String(item.category_id) === filterCategory;
    return matchesSearch && matchesCategory;
  });

  const formatWIB = (dateStr: string) => {
    if (!dateStr) return '-';
    return new Intl.DateTimeFormat('id-ID', {
      dateStyle: 'medium',
      timeStyle: 'short',
      timeZone: 'Asia/Jakarta'
    }).format(new Date(dateStr));
  };

  return (
    <div className="space-y-6 animate-in fade-in duration-500">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="space-y-1">
          <h1 className="text-2xl font-bold text-slate-900">Master Data Barang</h1>
          <p className="text-slate-500 hidden sm:block">Kelola semua item inventory Anda di sini.</p>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger
            render={
              <Button className="bg-[#6366f1] hover:bg-[#4f46e5] text-white shadow-lg shadow-indigo-500/20" onClick={() => {
                setEditingItem(null);
                setFormData({ name: '', category_id: '', unit: 'pcs', stock: 0, min_stock: 0 });
              }}>
                <Plus size={18} className="mr-2" /> Tambah Barang
              </Button>
            }
          />
          <DialogContent className="sm:max-w-[425px]">
            <DialogHeader>
              <DialogTitle>{editingItem ? 'Edit Barang' : 'Tambah Barang Baru'}</DialogTitle>
              <DialogDescription>
                Isi detail barang di bawah ini. Pastikan data sudah benar.
              </DialogDescription>
            </DialogHeader>
            <div className="grid gap-4 py-4">
              <div className="space-y-2">
                <label className="text-sm font-medium">Nama Barang</label>
                <Input 
                  value={formData.name} 
                  onChange={e => setFormData({...formData, name: e.target.value})} 
                  placeholder="Contoh: Biji Kopi Arabika / Susu Full Cream"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <label className="text-sm font-medium">Kategori</label>
                  <Select 
                    value={String(formData.category_id)} 
                    onValueChange={val => setFormData({...formData, category_id: val})}
                  >
                    <SelectTrigger className="w-full">
                      <SelectValue>
                        {categories.find(c => String(c.id) === String(formData.category_id))?.name || "Pilih Kategori"}
                      </SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                      {categories.map(cat => (
                        <SelectItem key={cat.id} value={String(cat.id)}>{cat.name}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium">Satuan</label>
                  <Select 
                    value={formData.unit} 
                    onValueChange={val => setFormData({...formData, unit: val as any})}
                  >
                    <SelectTrigger className="w-full">
                      <SelectValue placeholder="Satuan" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="pcs">Pcs</SelectItem>
                      <SelectItem value="kg">Kg</SelectItem>
                      <SelectItem value="liter">Liter</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <label className="text-sm font-medium">Stok Awal</label>
                  <Input 
                    type="number" 
                    value={formData.stock} 
                    onChange={e => setFormData({...formData, stock: parseInt(e.target.value) || 0})}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium">Stok Minimum</label>
                  <Input 
                    type="number" 
                    value={formData.min_stock} 
                    onChange={e => setFormData({...formData, min_stock: parseInt(e.target.value) || 0})}
                  />
                </div>
              </div>
            </div>
            <DialogFooter>
              <Button variant="outline" onClick={() => setIsDialogOpen(false)}>Batal</Button>
              <Button onClick={handleSave} className="bg-blue-600 hover:bg-blue-700 text-white">Simpan</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>

      {/* Filters */}
      <div className="flex flex-col lg:flex-row gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
          <Input 
            placeholder="Cari nama barang..." 
            className="pl-10 border-slate-200 focus:ring-blue-500"
            value={search}
            onChange={e => setSearch(e.target.value)}
          />
        </div>
        <div className="flex flex-wrap sm:flex-nowrap gap-2">
          <Select value={filterCategory} onValueChange={setFilterCategory}>
            <SelectTrigger className="w-full sm:w-[200px] border-slate-200">
              <Filter size={16} className="mr-2 text-slate-400" />
              <SelectValue>
                {filterCategory === 'all' ? 'Semua Kategori' : categories.find(c => String(c.id) === filterCategory)?.name || 'Kategori'}
              </SelectValue>
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Kategori</SelectItem>
              {categories.map(cat => (
                <SelectItem key={cat.id} value={String(cat.id)}>{cat.name}</SelectItem>
              ))}
            </SelectContent>
          </Select>
          <Button variant="outline" size="icon" onClick={fetchData} className="text-slate-500 shrink-0">
            <RefreshCw size={18} className={loading ? 'animate-spin' : ''} />
          </Button>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
          <TableHeader className="bg-slate-50">
            <TableRow>
              <TableHead className="w-[80px]">No</TableHead>
              <TableHead>Nama Barang</TableHead>
              <TableHead>Kategori</TableHead>
              <TableHead>Satuan</TableHead>
              <TableHead className="text-center">Stok</TableHead>
              <TableHead className="text-center">Status</TableHead>
              <TableHead className="text-right">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filteredItems.map((item, index) => {
              const category = categories.find(c => c.id === item.category_id);
              const isLow = item.stock <= item.min_stock && item.stock > 0;
              const isOut = item.stock === 0;

              return (
                <TableRow key={item.id} className="hover:bg-slate-50/50 transition-colors">
                  <TableCell className="font-medium text-slate-500">{index + 1}</TableCell>
                  <TableCell className="font-semibold text-slate-900">{item.name}</TableCell>
                  <TableCell>
                    <Badge variant="secondary" className="bg-slate-100 text-slate-600 border-none">
                      {category?.name || 'N/A'}
                    </Badge>
                  </TableCell>
                  <TableCell className="text-slate-600">{item.unit}</TableCell>
                  <TableCell className="text-center font-bold text-slate-900">{item.stock}</TableCell>
                  <TableCell className="text-center">
                    {isOut ? (
                      <Badge className="bg-[#fee2e2] text-[#991b1b] border-none text-[0.7rem] font-bold uppercase">Habis</Badge>
                    ) : isLow ? (
                      <Badge className="bg-[#fef3c7] text-[#92400e] border-none text-[0.7rem] font-bold uppercase">Menipis</Badge>
                    ) : (
                      <Badge className="bg-[#dcfce7] text-[#166534] border-none text-[0.7rem] font-bold uppercase">Tersedia</Badge>
                    )}
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-2">
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        className="text-slate-400 hover:text-blue-600 hover:bg-blue-50"
                        onClick={() => {
                          setEditingItem(item);
                          setFormData({
                            name: item.name,
                            category_id: String(item.category_id),
                            unit: item.unit,
                            stock: item.stock,
                            min_stock: item.min_stock
                          });
                          setIsDialogOpen(true);
                        }}
                      >
                        <Edit2 size={16} />
                      </Button>
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        className="text-slate-400 hover:text-red-600 hover:bg-red-50"
                        onClick={() => {
                          setItemToDelete(item.id);
                          setIsDeleteDialogOpen(true);
                        }}
                      >
                        <Trash2 size={16} />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              );
            })}
            {filteredItems.length === 0 && !loading && (
              <TableRow>
                <TableCell colSpan={7} className="h-32 text-center text-slate-400">
                  Tidak ada data barang ditemukan.
                </TableCell>
              </TableRow>
            )}
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
              Apakah Anda yakin ingin menghapus barang ini? Tindakan ini tidak dapat dibatalkan.
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
