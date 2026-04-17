export type Unit = 'kg' | 'pcs' | 'liter';
export type TransactionType = 'IN' | 'OUT' | 'ADJUST';

export interface Item {
  id: number;
  name: string;
  category_id: number;
  unit: Unit;
  stock: number;
  min_stock: number;
  created_at: string;
  updated_at: string;
}

export interface Category {
  id: number;
  name: string;
}

export interface Supplier {
  id: number;
  name: string;
  address: string;
  contact: string;
}

export interface Transaction {
  id: number;
  type: TransactionType;
  item_id: number;
  supplier_id?: number;
  quantity: number;
  destination?: string;
  note?: string;
  date: string;
  user_id: number;
}

export interface Log {
  id: number;
  user_id: number;
  action: string;
  details: string;
  timestamp: string;
}

export interface User {
  id: number;
  email: string;
  name: string;
  role: 'admin';
}
