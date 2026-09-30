namespace GeneradorProforma.Core.Entity;

public record ClienteInfo
{
  public required string Empresa { get; init; }
  public required string NombreCliente { get; init; }
  public required string CondicionPago { get; init; }
}

public record CondicionesInfo
{
  public required string TiempoFabricacion { get; init; }
  public required string ValidezOferta { get; init; }
}

public record Producto
{
  public required string Descripcion { get; init; }
  public required int Cantidad { get; init; }
  public required string Medida { get; init; }
  public required double PrecioUnitario { get; init; }
  public required double Total { get; init; }
}

public record Proforma
{
  public required string Id { get; init; }
  public required ClienteInfo Cliente { get; init; }
  public required List<Producto> Productos { get; init; }
  public required CondicionesInfo Condiciones { get; init; }
};
