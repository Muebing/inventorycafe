import React, { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2 } from 'lucide-react';
import { Button } from '../components/ui/button';
import { Input } from '../components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../components/ui/dialog';
import { toast } from 'sonner';
import { inventoryService } from '../services/inventoryService';

export default function Suppliers() {
  const [suppliers, setSuppliers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [supToDelete, setSupToDelete] = useState<number | null>(null);
  const [editingSup, setEditingSup] = useState<any>(null);
  const [formData, setFormData] = useState({ name: '', address: '', contact: '' });

  useEffect(() => { fetchSups(); }, []);

  const fetchSups = async () => {
    setLoading(true);
    try {
      const data = await inventoryService.getSuppliers();
      setSuppliers(data);
    } catch (e) { toast.error("Gagal ambil data"); }
    finally { setLoading(false); }
  };

  const handleSave = async () => {
    if (!formData.name) return;
    try {
      const url = editingSup ? `/api/suppliers/${editingSup.id}` : '/api/suppliers';
      const method = editingSup ? 'PUT' : 'POST';
      
      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(formData)
      });

      if (!res.ok) throw new Error("Failed to save");

      toast.success(editingSup ? "Supplier diperbarui" : "Supplier ditambahkan");
      setIsDialogOpen(false);
      setFormData({ name: '', address: '', contact: '' });
      setEditingSup(null);
      fetchSups();
    } catch (e) { toast.error("Gagal simpan"); }
  };

  const handleDelete = async () => {
    if (!supToDelete) return;
    try {
      const res = await fetch(`/api/suppliers/${supToDelete}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      if (!res.ok) throw new Error("Failed to delete");
      toast.success("Supplier dihapus");
      setIsDeleteDialogOpen(false);
      setSupToDelete(null);
      fetchSups();
    } catch (e) { toast.error("Gagal hapus"); }
  };

  return (
    <div className="space-y-6 animate-in fade-in duration-500">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 className="text-2xl font-bold text-slate-900">Data Supplier</h1>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger
            render={
              <Button className="bg-[#6366f1] hover:bg-[#4f46e5] text-white shadow-lg shadow-indigo-500/20" onClick={() => { setEditingSup(null); setFormData({ name: '', address: '', contact: '' }); }}>
                <Plus size={18} className="mr-2" /> Tambah Supplier
              </Button>
            }
          />
          <DialogContent>
            <DialogHeader><DialogTitle>{editingSup ? 'Edit' : 'Tambah'} Supplier</DialogTitle></DialogHeader>
            <div className="py-4 space-y-4">
              <div className="space-y-2">
                <label className="text-sm font-medium">Nama Supplier</label>
                <Input value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} placeholder="Nama Perusahaan" />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium">Alamat</label>
                <Input value={formData.address} onChange={e => setFormData({...formData, address: e.target.value})} placeholder="Alamat Lengkap" />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium">Kontak</label>
                <Input value={formData.contact} onChange={e => setFormData({...formData, contact: e.target.value})} placeholder="No. Telp / Email" />
              </div>
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
          <TableHeader><TableRow><TableHead>Nama</TableHead><TableHead>Alamat</TableHead><TableHead>Kontak</TableHead><TableHead className="text-right">Aksi</TableHead></TableRow></TableHeader>
          <TableBody>
            {suppliers.map((sup) => (
              <TableRow key={sup.id}>
                <TableCell className="font-medium">{sup.name}</TableCell>
                <TableCell className="text-slate-500">{sup.address}</TableCell>
                <TableCell className="text-slate-500">{sup.contact}</TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="icon" onClick={() => { setEditingSup(sup); setFormData({ name: sup.name, address: sup.address, contact: sup.contact }); setIsDialogOpen(true); }}>
                      <Edit2 size={16} />
                    </Button>
                    <Button variant="ghost" size="icon" className="text-red-500 hover:text-red-700" onClick={() => {
                      setSupToDelete(sup.id);
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
              Apakah Anda yakin ingin menghapus supplier ini? Tindakan ini tidak dapat dibatalkan.
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
