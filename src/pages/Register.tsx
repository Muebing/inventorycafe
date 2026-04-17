import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { authService } from '../lib/auth';
import { Button } from '../components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '../components/ui/card';
import { Package, Mail, Lock, User } from 'lucide-react';
import { Input } from '../components/ui/input';
import { toast } from 'sonner';

export default function Register() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({ name: '', email: '', password: '' });
  const [loading, setLoading] = useState(false);

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await authService.register(formData.name, formData.email, formData.password);
      toast.success("Registrasi berhasil, silakan login");
      navigate('/login');
    } catch (error: any) {
      toast.error(error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-[#1e1b4b] p-4">
      <Card className="w-full max-w-md border-none shadow-2xl bg-white rounded-2xl overflow-hidden">
        <CardHeader className="text-center space-y-4 pt-10">
          <div className="mx-auto w-16 h-16 bg-[#6366f1] rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-500/20">
            <Package size={32} />
          </div>
          <div>
            <CardTitle className="text-2xl font-extrabold text-[#1e293b] tracking-tight">Daftar Akun</CardTitle>
            <CardDescription className="text-slate-400 font-medium uppercase text-[0.7rem] tracking-widest">Inventory Management System</CardDescription>
          </div>
        </CardHeader>
        <CardContent className="px-8 pb-6">
          <form onSubmit={handleRegister} className="space-y-4">
            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Nama Lengkap</label>
              <div className="relative">
                <User className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                <Input 
                  placeholder="John Doe" 
                  className="pl-10"
                  value={formData.name}
                  onChange={e => setFormData({...formData, name: e.target.value})}
                  required
                />
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Email</label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                <Input 
                  type="email" 
                  placeholder="admin@example.com" 
                  className="pl-10"
                  value={formData.email}
                  onChange={e => setFormData({...formData, email: e.target.value})}
                  required
                />
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-slate-700">Password</label>
              <div className="relative">
                <Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                <Input 
                  type="password" 
                  placeholder="••••••••" 
                  className="pl-10"
                  value={formData.password}
                  onChange={e => setFormData({...formData, password: e.target.value})}
                  required
                />
              </div>
            </div>
            <Button 
              type="submit"
              disabled={loading}
              className="w-full h-12 bg-[#6366f1] hover:bg-[#4f46e5] text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20"
            >
              {loading ? "Mendaftar..." : "Daftar Sekarang"}
            </Button>
          </form>
        </CardContent>
        <CardFooter className="bg-slate-50 py-4 text-center border-t border-slate-100 flex flex-col gap-2">
          <p className="text-sm text-slate-600">
            Sudah punya akun? <Link to="/login" className="text-[#6366f1] font-bold hover:underline">Login</Link>
          </p>
        </CardFooter>
      </Card>
    </div>
  );
}
