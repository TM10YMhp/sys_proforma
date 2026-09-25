namespace GeneradorProforma.Core.Entity;

public record ClienteInfo(
  string Empresa = "",
  string NombreCliente = "",
  string CondicionPago = ""
);

public record CondicionesInfo(
  string TiempoFabricacion = "",
  string ValidezOferta = ""
);

public record Producto(
  string Descripcion = "",
  int Cantidad = 0,
  string Medida = "",
  double PrecioUnitario = 0,
  double Total = 0
);
