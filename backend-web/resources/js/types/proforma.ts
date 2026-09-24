import type { Product } from './product';

export interface Proforma {
  id: number;

  codigo: string;
  fecha_emision: string;
  fecha_vencimiento: string;
  subtotal: number;
  igv_tasa: number;
  igv_monto: number;
  total: number;

  products: Product[];

  created_at: string;
  updated_at: string;
}
