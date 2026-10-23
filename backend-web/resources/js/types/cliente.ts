// TODO: php no deberia enviar null
export interface Cliente {
  id: number;

  nombres: string;
  apellido_primario: string | null;
  apellido_secundario: string | null;
  ruc: string | null;
  dni: string | null;
  telefono: string | null;

  created_at: string;
  updated_at: string;
}
