import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { 
  LayoutDashboard, 
  Package, 
  Tags, 
  Truck, 
  ArrowLeftRight, 
  History, 
  LogOut, 
  Menu, 
  X,
  Bell
} from 'lucide-react';
import { authService } from '../lib/auth';
import { Button } from './ui/button';
import { ScrollArea } from './ui/scroll-area';
import { Separator } from './ui/separator';
import { Badge } from './ui/badge';

export default function Layout({ children }: { children: React.ReactNode }) {
  const [isSidebarOpen, setIsSidebarOpen] = useState(window.innerWidth > 768);
  const [isNotificationOpen, setIsNotificationOpen] = useState(false);
  const [notifications, setNotifications] = useState<any[]>([]);
  const [user, setUser] = useState<any>(null);
  const navigate = useNavigate();
  const location = useLocation();

  useEffect(() => {
    if (!authService.isAuthenticated()) {
      navigate('/login');
    } else {
      setUser(authService.getCurrentUser());
      fetchNotifications();
    }
    // Close sidebar on mobile when navigating
    if (window.innerWidth < 768) {
      setIsSidebarOpen(false);
    }
  }, [navigate, location.pathname]);

  const fetchNotifications = async () => {
    try {
      const res = await fetch('/api/stats', {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      const data = await res.json();
      const lowStockItems = data.items.filter((item: any) => item.stock <= item.min_stock);
      setNotifications(lowStockItems);
    } catch (error) {
      console.error("Failed to fetch notifications", error);
    }
  };

  const navItems = [
    { name: 'Dashboard', path: '/', icon: LayoutDashboard },
    { name: 'Barang', path: '/items', icon: Package },
    { name: 'Kategori', path: '/categories', icon: Tags },
    { name: 'Supplier', path: '/suppliers', icon: Truck },
    { name: 'Transaksi', path: '/transactions', icon: ArrowLeftRight },
    { name: 'Log Activity', path: '/logs', icon: History },
  ];

  const handleLogout = () => {
    authService.logout();
    navigate('/login');
  };

  if (!authService.isAuthenticated() && location.pathname !== '/login' && location.pathname !== '/register') return null;

  return (
    <div className="flex h-screen bg-slate-50 font-sans text-slate-900 overflow-hidden">
      {/* Sidebar Overlay for Mobile */}
      {isSidebarOpen && (
        <div 
          className="fixed inset-0 bg-slate-900/50 z-40 md:hidden animate-in fade-in duration-300" 
          onClick={() => setIsSidebarOpen(false)}
        ></div>
      )}

      {/* Sidebar */}
      <aside 
        className={`bg-[#1e1b4b] text-white transition-all duration-300 ease-in-out fixed h-full z-50 
          ${isSidebarOpen ? 'w-64 translate-x-0' : 'w-20 -translate-x-full md:translate-x-0'} 
          ${!isSidebarOpen && 'md:w-20'}
        `}
      >
        <div className="p-6 flex items-center justify-between border-b border-white/10">
          {(isSidebarOpen || !isSidebarOpen) && (
            <h1 className={`text-xl font-extrabold tracking-widest text-white uppercase ${!isSidebarOpen && 'md:hidden'}`}>
              INV-PRO
            </h1>
          )}
          <Button 
            variant="ghost" 
            size="icon" 
            onClick={() => setIsSidebarOpen(!isSidebarOpen)}
            className="text-white/50 hover:text-white hover:bg-white/5"
          >
            {isSidebarOpen ? <X size={20} /> : <Menu size={20} />}
          </Button>
        </div>

        <ScrollArea className="flex-1 px-0 py-4">
          <nav className="space-y-0">
            {navItems.map((item) => (
              <Link
                key={item.path}
                to={item.path}
                className={`flex items-center px-6 py-3 transition-all ${
                  location.pathname === item.path 
                    ? 'bg-white/5 border-l-4 border-[#6366f1] text-white opacity-100' 
                    : 'text-white opacity-70 hover:opacity-100 hover:bg-white/3'
                }`}
              >
                <item.icon size={20} className={isSidebarOpen ? 'mr-3' : 'mx-auto'} />
                {isSidebarOpen && <span className="text-[0.9rem] font-medium">{item.name}</span>}
              </Link>
            ))}
          </nav>
        </ScrollArea>

        <div className="p-4 mt-auto">
          <Separator className="bg-white/10 mb-4" />
          <Button 
            variant="ghost" 
            className="w-full justify-start text-white/50 hover:text-white hover:bg-red-500/10"
            onClick={handleLogout}
          >
            <LogOut size={20} className={isSidebarOpen ? 'mr-3' : 'mx-auto'} />
            {isSidebarOpen && <span className="text-[0.9rem]">Logout</span>}
          </Button>
        </div>
      </aside>

      {/* Main Content */}
      <main className={`flex-1 flex flex-col min-w-0 transition-all duration-300 ${isSidebarOpen ? 'md:ml-64' : 'md:ml-20'}`}>
        {/* Topbar */}
        <header className="h-16 bg-white border-b border-[#e2e8f0] flex items-center justify-between px-4 md:px-8 sticky top-0 z-40">
          <div className="flex items-center gap-4">
            <Button 
              variant="ghost" 
              size="icon" 
              onClick={() => setIsSidebarOpen(true)}
              className="md:hidden text-slate-500"
            >
              <Menu size={20} />
            </Button>
            <h2 className="text-sm font-bold uppercase tracking-wider text-[#1e293b] truncate">
              {navItems.find(i => i.path === location.pathname)?.name || 'Inventory Overview'}
            </h2>
          </div>
          
          <div className="flex items-center gap-4">
            <div className="relative">
              <Button 
                variant="ghost" 
                size="icon" 
                className={`relative text-slate-400 hover:text-slate-600 ${isNotificationOpen ? 'bg-slate-100' : ''}`}
                onClick={() => setIsNotificationOpen(!isNotificationOpen)}
              >
                <Bell size={20} />
                {notifications.length > 0 && (
                  <span className="absolute top-2 right-2 w-2 h-2 bg-[#f59e0b] rounded-full"></span>
                )}
              </Button>

              {isNotificationOpen && (
                <>
                  <div 
                    className="fixed inset-0 z-40" 
                    onClick={() => setIsNotificationOpen(false)}
                  ></div>
                  <div className="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-200 origin-top-right">
                    <div className="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                      <h3 className="font-bold text-sm text-slate-900">Notifikasi</h3>
                      <Badge variant="secondary" className="bg-amber-100 text-amber-700 border-none text-[10px]">
                        {notifications.length} Peringatan
                      </Badge>
                    </div>
                    <ScrollArea className="max-h-[300px]">
                      <div className="p-2">
                        {notifications.length > 0 ? (
                          notifications.map((item) => (
                            <div key={item.id} className="p-3 hover:bg-slate-50 rounded-lg transition-colors flex gap-3 items-start">
                              <div className="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                <Package size={14} className="text-amber-600" />
                              </div>
                              <div className="flex-1">
                                <p className="text-xs font-bold text-slate-900">Stok Menipis!</p>
                                <p className="text-[11px] text-slate-500 mt-0.5">
                                  Barang <span className="font-semibold text-slate-700">{item.name}</span> tersisa <span className="font-bold text-amber-600">{item.stock} {item.unit}</span>.
                                </p>
                              </div>
                            </div>
                          ))
                        ) : (
                          <div className="p-8 text-center">
                            <Bell size={24} className="mx-auto text-slate-200 mb-2" />
                            <p className="text-xs text-slate-400">Tidak ada notifikasi baru</p>
                          </div>
                        )}
                      </div>
                    </ScrollArea>
                    <div className="p-3 border-t border-slate-100 bg-slate-50/50 text-center">
                      <Link 
                        to="/items" 
                        className="text-[11px] font-bold text-[#6366f1] hover:underline"
                        onClick={() => setIsNotificationOpen(false)}
                      >
                        Lihat Semua Barang
                      </Link>
                    </div>
                  </div>
                </>
              )}
            </div>

            <div className="flex items-center gap-3 pl-4 border-l border-slate-100">
              <div className="text-right hidden sm:block">
                <p className="text-sm font-bold text-[#334155]">{user?.name || 'Administrator'}</p>
                <p className="text-[0.7rem] font-bold uppercase text-slate-400">Staff Admin</p>
              </div>
              <div className="w-8 h-8 rounded-full bg-[#6366f1] flex items-center justify-center text-white text-xs font-bold">
                {user?.name?.[0].toUpperCase() || 'AD'}
              </div>
            </div>
          </div>
        </header>

        {/* Page Content */}
        <div className="p-4 md:p-8 overflow-auto flex-1">
          {children}
        </div>
      </main>
    </div>
  );
}
