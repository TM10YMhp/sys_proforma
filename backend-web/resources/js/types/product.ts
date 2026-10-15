export interface Product {
  id: number;
  codigo: string;
  descripcion: string;
  precio: number;
  unidad_medida: string;
  stock: number;
  activo: boolean;
  created_at: string;
  updated_at: string;
}

export interface ProductItem {
  id: number;
  codigo: string;
  descripcion: string;
  precio: number;
  unidad_medida: string;
  cantidad: number;
  activo: boolean;
}
