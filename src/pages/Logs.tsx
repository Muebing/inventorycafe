import React, { useState, useEffect } from 'react';
import { 
  RefreshCw, 
  Search,
  User,
  Clock
} from 'lucide-react';
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
import { Badge } from '../components/ui/badge';
import { toast } from 'sonner';
import { format } from 'date-fns';
import { inventoryService } from '../services/inventoryService';

export default function Logs() {
  const [logs, setLogs] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [filterDate, setFilterDate] = useState('');

  useEffect(() => {
    fetchLogs();
  }, []);

  const fetchLogs = async () => {
    setLoading(true);
    try {
      const data = await inventoryService.getLogs();
      setLogs(data);
    } catch (error) {
      toast.error("Gagal mengambil data log");
    } finally {
      setLoading(false);
    }
  };

  const filteredLogs = logs.filter(log => {
    const matchesSearch = log.details?.toLowerCase().includes(search.toLowerCase()) ||
                         log.action?.toLowerCase().includes(search.toLowerCase()) ||
                         log.user_name?.toLowerCase().includes(search.toLowerCase());
    const matchesDate = !filterDate || (log.timestamp && log.timestamp.startsWith(filterDate));
    return matchesSearch && matchesDate;
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
          <h1 className="text-2xl font-bold text-slate-900">Log Activity</h1>
          <p className="text-slate-500 hidden sm:block">Riwayat aktivitas sistem dan perubahan data.</p>
        </div>
        <Button variant="outline" onClick={fetchLogs} disabled={loading} className="w-full sm:w-auto">
          <RefreshCw size={18} className={`mr-2 ${loading ? 'animate-spin' : ''}`} /> Refresh
        </Button>
      </div>

      <div className="flex flex-col lg:flex-row gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
          <Input 
            placeholder="Cari aktivitas atau user..." 
            className="pl-10 border-slate-200"
            value={search}
            onChange={e => setSearch(e.target.value)}
          />
        </div>
        <div className="flex flex-wrap sm:flex-nowrap gap-2">
          <div className="relative flex-1 sm:flex-none">
            <Clock className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={16} />
            <Input 
              type="date" 
              className="pl-10 border-slate-200 w-full sm:w-[180px]" 
              value={filterDate}
              onChange={e => setFilterDate(e.target.value)}
            />
          </div>
          <Button variant="outline" size="icon" onClick={() => { setSearch(''); setFilterDate(''); fetchLogs(); }} className="text-slate-500 shrink-0">
            <RefreshCw size={18} className={loading ? 'animate-spin' : ''} />
          </Button>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
          <TableHeader className="bg-slate-50">
            <TableRow>
              <TableHead className="w-[200px]">Waktu</TableHead>
              <TableHead>Nama</TableHead>
              <TableHead>Aktivitas</TableHead>
              <TableHead>Detail</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filteredLogs.map((log) => (
              <TableRow key={log.id} className="hover:bg-slate-50/50 transition-colors">
                <TableCell className="text-slate-500 text-sm">
                  <div className="flex items-center gap-2">
                    <Clock size={14} />
                    {formatWIB(log.timestamp)}
                  </div>
                </TableCell>
                <TableCell>
                  <div className="flex items-center gap-2">
                    <div className="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-600">
                      <User size={12} />
                    </div>
                    <span className="text-sm font-medium text-slate-700">{log.user_name || `User #${log.user_id}`}</span>
                  </div>
                </TableCell>
                <TableCell>
                  <Badge variant="outline" className="bg-blue-50 text-blue-700 border-blue-100">
                    {log.action}
                  </Badge>
                </TableCell>
                <TableCell className="text-slate-600 text-sm max-w-md truncate">
                  {log.details}
                </TableCell>
              </TableRow>
            ))}
            {filteredLogs.length === 0 && !loading && (
              <TableRow>
                <TableCell colSpan={4} className="h-32 text-center text-slate-400">
                  Tidak ada log aktivitas ditemukan.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
    </div>
    </div>
  );
}
