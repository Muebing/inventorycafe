import { Item, Category, Supplier, Transaction, Log } from '../types';

const API_URL = '/api';

const getAuthHeaders = () => {
  const token = localStorage.getItem('token');
  return {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  };
};

export const inventoryService = {
  async getItems(): Promise<Item[]> {
    const res = await fetch(`${API_URL}/items`, { headers: getAuthHeaders() });
    return res.json();
  },

  async getCategories(): Promise<Category[]> {
    const res = await fetch(`${API_URL}/categories`, { headers: getAuthHeaders() });
    return res.json();
  },

  async getSuppliers(): Promise<Supplier[]> {
    const res = await fetch(`${API_URL}/suppliers`, { headers: getAuthHeaders() });
    return res.json();
  },

  async processTransaction(data: any): Promise<void> {
    const res = await fetch(`${API_URL}/transactions`, {
      method: 'POST',
      headers: getAuthHeaders(),
      body: JSON.stringify(data)
    });
    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.error || 'Transaction failed');
    }
  },

  async getDashboardStats(): Promise<any> {
    const res = await fetch(`${API_URL}/stats`, { headers: getAuthHeaders() });
    return res.json();
  },

  async getLogs(): Promise<Log[]> {
    const res = await fetch(`${API_URL}/logs`, { headers: getAuthHeaders() });
    return res.json();
  }
};
