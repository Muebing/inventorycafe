import React, { useState, useEffect } from 'react';
import { 
  ArrowLeftRight, 
  Plus, 
  Minus, 
  RefreshCw, 
  Search,
  Calendar as CalendarIcon,
  ArrowUpCircle,
  ArrowDownCircle,
  Settings2,
  Edit2,
  Trash2,
  Filter
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
import { format } from 'date-fns';

export default function Transactions() {
  const [transactions, setTransactions] = useState<any[]>([]);
  const [items, setItems] = useState<any[]>([]);
  const [suppliers, setSuppliers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [transactionToDelete, setTransactionToDelete] = useState<number | null>(null);
  const [editingTransaction, setEditingTransaction] = useState<any>(null);
  const [search, setSearch] = useState('');
  const [filterDate, setFilterDate] = useState('');
  const [filterType, setFilterType] = useState('all');

  // Form State
  const [formData, setFormData] = useState({
    type: 'IN' as any,
    itemId: '',
    supplierId: '',
    quantity: 1,
    destination: '',
    note: ''
  });

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [itemsData, suppliersData, transactionsData] = await Promise.all([
        inventoryService.getItems(),
        inventoryService.getSuppliers(),
        fetch('/api/transactions', {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        }).then(res => res.json())
      ]);
      setItems(itemsData);
      setSuppliers(suppliersData);
      setTransactions(transactionsData);
    } catch (error) {
      toast.error("Gagal mengambil data");
    } finally {
      setLoading(false);
    }
  };

  const handleProcess = async () => {
    if (!formData.itemId || !formData.quantity) {
      toast.error("Mohon lengkapi data");
      return;
    }

    try {
      const url = editingTransaction ? `/api/transactions/${editingTransaction.id}` : '/api/transactions';
      const method = editingTransaction ? 'PUT' : 'POST';
      
      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(formData)
      });

      if (!res.ok) {
        const err = await res.json();
        throw new Error(err.error || "Gagal memproses");
      }

      toast.success(editingTransaction ? "Transaksi diperbarui" : "Transaksi berhasil diproses");
      setIsDialogOpen(false);
      setEditingTransaction(null);
      setFormData({ type: 'IN', itemId: '', supplierId: '', quantity: 1, destination: '', note: '' });
      fetchData();
    } catch (error: any) {
      toast.error(error.message || "Gagal memproses transaksi");
    }
  };

  const handleDelete = async () => {
    if (!transactionToDelete) return;
    try {
      const res = await fetch(`/api/transactions/${transactionToDelete}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      if (!res.ok) throw new Error("Gagal menghapus");
      toast.success("Transaksi dihapus");
      setIsDeleteDialogOpen(false);
      setTransactionToDelete(null);
      fetchData();
    } catch (error) {
      toast.error("Gagal menghapus transaksi");
    }
  };

  const filteredTransactions = transactions.filter(t => {
    const item = items.find(i => i.id === t.item_id);
    const itemName = item?.name || t.item_name || '';
    const matchesSearch = itemName.toLowerCase().includes(search.toLowerCase()) || 
                         (t.note?.toLowerCase() || '').includes(search.toLowerCase()) ||
                         (t.destination?.toLowerCase() || '').includes(search.toLowerCase());
    const matchesDate = !filterDate || (t.date && t.date.startsWith(filterDate));
    const matchesType = filterType === 'all' || t.type === filterType;
    return matchesSearch && matchesDate && matchesType;
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
          <h1 className="text-2xl font-bold text-slate-900">Inventory Management</h1>
          <p className="text-slate-500 hidden sm:block">Kelola barang masuk, keluar, dan penyesuaian stok.</p>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger
            render={
              <Button className="bg-[#6366f1] hover:bg-[#4f46e5] text-white shadow-lg shadow-indigo-500/20" onClick={() => {
                setEditingTransaction(null);
                setFormData({ type: 'IN', itemId: '', supplierId: '', quantity: 1, destination: '', note: '' });
              }}>
                <Plus size={18} className="mr-2" /> Transaksi Baru
              </Button>
            }
          />
          <DialogContent className="sm:max-w-[500px]">
            <DialogHeader>
              <DialogTitle>{editingTransaction ? 'Edit Transaksi' : 'Proses Transaksi Baru'}</DialogTitle>
              <DialogDescription>
                {editingTransaction ? 'Ubah detail transaksi yang dipilih.' : 'Pilih tipe transaksi dan isi detail barang.'}
              </DialogDescription>
            </DialogHeader>
            <div className="grid gap-6 py-4">
              <div className="grid grid-cols-3 gap-2">
                <Button 
                  variant={formData.type === 'IN' ? 'default' : 'outline'} 
                  className={formData.type === 'IN' ? 'bg-emerald-600 hover:bg-emerald-700' : ''}
                  onClick={() => setFormData({...formData, type: 'IN'})}
                >
                  <ArrowUpCircle size={16} className="mr-2" /> In
                </Button>
                <Button 
                  variant={formData.type === 'OUT' ? 'default' : 'outline'} 
                  className={formData.type === 'OUT' ? 'bg-red-600 hover:bg-red-700' : ''}
                  onClick={() => setFormData({...formData, type: 'OUT'})}
                >
                  <ArrowDownCircle size={16} className="mr-2" /> Out
                </Button>
                <Button 
                  variant={formData.type === 'ADJUST' ? 'default' : 'outline'} 
                  className={formData.type === 'ADJUST' ? 'bg-amber-600 hover:bg-amber-700' : ''}
                  onClick={() => setFormData({...formData, type: 'ADJUST'})}
                >
                  <Settings2 size={16} className="mr-2" /> Adjust
                </Button>
              </div>

              <div className="space-y-2">
                <label className="text-sm font-medium">Pilih Barang</label>
                <Select value={String(formData.itemId)} onValueChange={val => setFormData({...formData, itemId: val})}>
                  <SelectTrigger className="w-full">
                    <SelectValue>
                      {items.find(i => String(i.id) === String(formData.itemId))?.name || "Pilih Barang"}
                    </SelectValue>
                  </SelectTrigger>
                  <SelectContent>
                    {items.map(item => (
                      <SelectItem key={item.id} value={String(item.id)}>{item.name} (Stok: {item.stock})</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              {formData.type === 'IN' && (
                <div className="space-y-2">
                  <label className="text-sm font-medium">Supplier</label>
                  <Select value={String(formData.supplierId)} onValueChange={val => setFormData({...formData, supplierId: val})}>
                    <SelectTrigger className="w-full">
                      <SelectValue>
                        {suppliers.find(s => String(s.id) === String(formData.supplierId))?.name || "Pilih Supplier"}
                      </SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                      {suppliers.map(sup => (
                        <SelectItem key={sup.id} value={String(sup.id)}>{sup.name}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              )}

              {formData.type === 'OUT' && (
                <div className="space-y-2">
                  <label className="text-sm font-medium">Tujuan (Produksi / Penjualan)</label>
                  <Input 
                    placeholder="Contoh: Penjualan Toko A" 
                    value={formData.destination}
                    onChange={e => setFormData({...formData, destination: e.target.value})}
                  />
                </div>
              )}

              {formData.type === 'ADJUST' && (
                <div className="space-y-2">
                  <label className="text-sm font-medium">Keterangan Penyesuaian</label>
                  <Input 
                    placeholder="Contoh: Barang rusak / Selisih opname" 
                    value={formData.note}
                    onChange={e => setFormData({...formData, note: e.target.value})}
                  />
                </div>
              )}

              <div className="space-y-2">
                <label className="text-sm font-medium">
                  {formData.type === 'ADJUST' ? 'Stok Sebenarnya' : 'Jumlah'}
                </label>
                <Input 
                  type="number" 
                  min="1"
                  value={formData.quantity}
                  onChange={e => setFormData({...formData, quantity: parseInt(e.target.value) || 0})}
                />
              </div>
            </div>
            <DialogFooter>
              <Button variant="outline" onClick={() => setIsDialogOpen(false)}>Batal</Button>
              <Button onClick={handleProcess} className="bg-blue-600 hover:bg-blue-700 text-white">Proses Transaksi</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>

      {/* Filters */}
      <div className="flex flex-col lg:flex-row gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
          <Input 
            placeholder="Cari barang atau keterangan..." 
            className="pl-10 border-slate-200"
            value={search}
            onChange={e => setSearch(e.target.value)}
          />
        </div>
        <div className="flex flex-wrap sm:flex-nowrap gap-2">
          <Select value={filterType} onValueChange={setFilterType}>
            <SelectTrigger className="w-full sm:w-[150px] border-slate-200">
              <Filter size={16} className="mr-2 text-slate-400" />
              <SelectValue>
                {filterType === 'all' ? 'Semua Tipe' : filterType === 'IN' ? 'Stock In' : filterType === 'OUT' ? 'Stock Out' : 'Adjust'}
              </SelectValue>
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Tipe</SelectItem>
              <SelectItem value="IN">Stock In</SelectItem>
              <SelectItem value="OUT">Stock Out</SelectItem>
              <SelectItem value="ADJUST">Adjust</SelectItem>
            </SelectContent>
          </Select>
          <div className="relative flex-1 sm:flex-none">
            <CalendarIcon className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={16} />
            <Input 
              type="date" 
              className="pl-10 border-slate-200 w-full sm:w-[180px]" 
              value={filterDate}
              onChange={e => setFilterDate(e.target.value)}
            />
          </div>
          <Button variant="outline" size="icon" onClick={() => { setSearch(''); setFilterDate(''); setFilterType('all'); fetchData(); }} className="text-slate-500 shrink-0">
            <RefreshCw size={18} className={loading ? 'animate-spin' : ''} />
          </Button>
        </div>
      </div>

      {/* Recent Transactions Table */}
      <div className="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="p-4 border-b border-slate-100 flex items-center justify-between">
          <h3 className="font-bold text-slate-900">Riwayat Transaksi</h3>
        </div>
        <div className="overflow-x-auto">
          <Table>
          <TableHeader className="bg-slate-50">
            <TableRow>
              <TableHead>Tanggal</TableHead>
              <TableHead>Tipe</TableHead>
              <TableHead>Barang</TableHead>
              <TableHead className="text-center">Jumlah</TableHead>
              <TableHead>Keterangan</TableHead>
              <TableHead className="text-right">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filteredTransactions.map((t) => {
              const supplier = suppliers.find(s => s.id === t.supplier_id);
              
              return (
                <TableRow key={t.id}>
                  <TableCell className="text-slate-500 text-sm">
                    {formatWIB(t.date)}
                  </TableCell>
                  <TableCell>
                    {t.type === 'IN' ? (
                      <Badge className="bg-[#dcfce7] text-[#166534] border-none text-[0.7rem] font-bold uppercase">Stock In</Badge>
                    ) : t.type === 'OUT' ? (
                      <Badge className="bg-[#fee2e2] text-[#991b1b] border-none text-[0.7rem] font-bold uppercase">Stock Out</Badge>
                    ) : (
                      <Badge className="bg-[#fef3c7] text-[#92400e] border-none text-[0.7rem] font-bold uppercase">Adjust</Badge>
                    )}
                  </TableCell>
                  <TableCell className="font-medium text-slate-900">{t.item_name || 'N/A'}</TableCell>
                  <TableCell className="text-center font-bold">
                    {t.type === 'OUT' ? '-' : t.type === 'IN' ? '+' : ''}{Math.abs(t.quantity)}
                  </TableCell>
                  <TableCell className="text-slate-500 text-sm">
                    {t.type === 'IN' && supplier ? `Dari: ${supplier.name}` : ''}
                    {t.type === 'OUT' && t.destination ? `Ke: ${t.destination}` : ''}
                    {t.type === 'ADJUST' && t.note ? t.note : ''}
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-2">
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        className="text-slate-400 hover:text-blue-600 hover:bg-blue-50"
                        onClick={() => {
                          setEditingTransaction(t);
                          setFormData({
                            type: t.type,
                            itemId: String(t.item_id),
                            supplierId: t.supplier_id ? String(t.supplier_id) : '',
                            quantity: Math.abs(t.quantity),
                            destination: t.destination || '',
                            note: t.note || ''
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
                          setTransactionToDelete(t.id);
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
            {filteredTransactions.length === 0 && !loading && (
              <TableRow>
                <TableCell colSpan={6} className="h-32 text-center text-slate-400">
                  Belum ada transaksi ditemukan.
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
              Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.
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
