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

  products: (Product & { pivot: ProformaItem })[];

  created_at: string;
  updated_at: string;
}

interface ProformaItem {
  id: number;

  proforma_id: string;
  product_id: string;
  cantidad: number;
  precio_unitario: number;
  subtotal: number;

  created_at: string;
  updated_at: string;
}
