import React, { useState, useEffect } from 'react';
import { 
  Package, 
  AlertTriangle, 
  XCircle, 
  TrendingUp, 
  ArrowUpRight, 
  ArrowDownRight,
  Plus,
  Minus,
  RefreshCw,
  Bell
} from 'lucide-react';
import { inventoryService } from '../services/inventoryService';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '../components/ui/card';
import { Alert, AlertDescription, AlertTitle } from '../components/ui/alert';
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer,
  Cell,
  PieChart,
  Pie
} from 'recharts';
import { Button } from '../components/ui/button';
import { Badge } from '../components/ui/badge';
import { ScrollArea } from '../components/ui/scroll-area';

export default function Dashboard() {
  const [stats, setStats] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    setLoading(true);
    try {
      const data = await inventoryService.getDashboardStats();
      setStats(data);
    } catch (error) {
      console.error("Failed to fetch dashboard stats", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div className="flex items-center justify-center h-full"><RefreshCw className="animate-spin text-blue-600" /></div>;

  const lowStockItems = stats?.items?.filter((i: any) => i.stock <= i.min_stock) || [];
  const outOfStockItems = stats?.items?.filter((i: any) => i.stock === 0) || [];

  const chartData = stats?.items?.slice(0, 5).map((i: any) => ({
    name: i.name,
    stock: i.stock,
    min: i.min_stock
  })) || [];

  const COLORS = ['#6366f1', '#8b5cf6', '#10b981', '#f59e0b', '#3b82f6'];

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card className="border-none shadow-md bg-white overflow-hidden border-l-4 border-[#6366f1]">
          <CardHeader className="pb-2">
            <CardTitle className="text-[0.75rem] font-bold text-[#64748b] uppercase tracking-widest">Total Semua Barang</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-extrabold text-[#334155]">{stats?.totalItems || 0}</div>
          </CardContent>
        </Card>

        <Card className="border-none shadow-md bg-white overflow-hidden border-l-4 border-[#f59e0b]">
          <CardHeader className="pb-2">
            <CardTitle className="text-[0.75rem] font-bold text-[#64748b] uppercase tracking-widest">Stok Menipis (Low Stock)</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-extrabold text-[#334155]">{stats?.lowStock || 0}</div>
          </CardContent>
        </Card>

        <Card className="border-none shadow-md bg-white overflow-hidden border-l-4 border-[#ef4444]">
          <CardHeader className="pb-2">
            <CardTitle className="text-[0.75rem] font-bold text-[#64748b] uppercase tracking-widest">Barang Habis (Out of Stock)</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-extrabold text-[#334155]">{stats?.outOfStock || 0}</div>
          </CardContent>
        </Card>
      </div>

      {/* Alerts Section */}
      {(lowStockItems.length > 0 || outOfStockItems.length > 0) && (
        <div className="bg-[#fffbeb] border border-[#fde68a] rounded-xl p-4 flex items-center gap-3 text-[#92400e] text-sm shadow-sm">
          <div className="w-2 h-2 bg-[#f59e0b] rounded-full animate-pulse"></div>
          <div>
            <span className="font-bold">Perhatian!</span> Ada {lowStockItems.length + outOfStockItems.length} item dengan stok kritis. Segera lakukan pengadaan.
          </div>
        </div>
      )}

      {/* Charts Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <Card className="border-none shadow-sm bg-white">
          <CardHeader>
            <CardTitle className="text-lg font-bold text-slate-900">Statistik Stok Barang</CardTitle>
            <CardDescription>Perbandingan stok saat ini dengan batas minimum</CardDescription>
          </CardHeader>
          <CardContent className="h-[350px]">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={chartData}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{fill: '#64748b', fontSize: 12}} />
                <YAxis axisLine={false} tickLine={false} tick={{fill: '#64748b', fontSize: 12}} />
                <Tooltip 
                  contentStyle={{backgroundColor: '#fff', borderRadius: '8px', border: 'none', boxShadow: '0 4px 12px rgba(0,0,0,0.1)'}}
                  cursor={{fill: '#f8fafc'}}
                />
                <Bar dataKey="stock" fill="#3b82f6" radius={[4, 4, 0, 0]} barSize={40} />
                <Bar dataKey="min" fill="#cbd5e1" radius={[4, 4, 0, 0]} barSize={40} />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        <Card className="border-none shadow-sm bg-white">
          <CardHeader>
            <CardTitle className="text-lg font-bold text-slate-900">Distribusi Kategori</CardTitle>
            <CardDescription>Persentase barang berdasarkan kategori</CardDescription>
          </CardHeader>
          <CardContent className="h-[350px] flex items-center justify-center">
             <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={chartData}
                  innerRadius={80}
                  outerRadius={120}
                  paddingAngle={5}
                  dataKey="stock"
                >
                  {chartData.map((entry: any, index: number) => (
                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      {/* Recent Activity Mini Table */}
      <Card className="border-none shadow-sm bg-white">
        <CardHeader className="flex flex-row items-center justify-between">
          <div>
            <CardTitle className="text-lg font-bold text-slate-900">Barang dengan Stok Rendah</CardTitle>
            <CardDescription>Segera lakukan pengadaan untuk barang-barang berikut</CardDescription>
          </div>
          <Button variant="outline" size="sm" onClick={() => fetchStats()}>
            <RefreshCw size={14} className="mr-2" /> Refresh
          </Button>
        </CardHeader>
        <CardContent>
          <ScrollArea className="h-[300px]">
            <div className="space-y-4">
              {lowStockItems.map((item: any) => (
                <div key={item.id} className="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                  <div className="flex items-center gap-4">
                    <div className="w-10 h-10 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                      <Package size={20} />
                    </div>
                    <div>
                      <p className="font-bold text-slate-900">{item.name}</p>
                      <p className="text-xs text-slate-500">Min: {item.min_stock} {item.unit}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <Badge variant="outline" className="bg-amber-50 text-amber-700 border-amber-200">
                      Sisa {item.stock} {item.unit}
                    </Badge>
                  </div>
                </div>
              ))}
              {lowStockItems.length === 0 && (
                <div className="text-center py-12 text-slate-400">
                  <p>Semua stok dalam kondisi aman.</p>
                </div>
              )}
            </div>
          </ScrollArea>
        </CardContent>
      </Card>
    </div>
  );
}
