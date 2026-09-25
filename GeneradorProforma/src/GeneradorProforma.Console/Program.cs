using GeneradorProforma.Core.Entity;
using GeneradorProforma.Core.Service;

internal class Program
{
  private static readonly string _id = "001 - 2547";

  private static readonly ClienteInfo _cliente = new()
  {
    Empresa = "Safresco Peru SAC",
    NombreCliente = "Paul Sanchez",
    CondicionPago = "Credito 15 dias",
  };

  private static readonly CondicionesInfo _condiciones = new()
  {
    TiempoFabricacion = "2 dias",
    ValidezOferta = "7 dias",
  };

  private static readonly List<Producto> _products =
  [
    new()
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 22 cm",
      Cantidad = 27,
      Medida = "UN",
      PrecioUnitario = 10,
      Total = 270,
    },
    new()
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 60 cm x 30 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 24,
      Total = 480,
    },
    new()
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 30 cm",
      Cantidad = 8,
      Medida = "UN",
      PrecioUnitario = 12.5,
      Total = 100,
    },
    new()
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 25 cm",
      Cantidad = 2,
      Medida = "UN",
      PrecioUnitario = 10,
      Total = 20,
    },
    new()
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 100 cm x 60 cm",
      Cantidad = 6,
      Medida = "UN",
      PrecioUnitario = 65,
      Total = 390,
    },
    new()
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 40 cm x 30 cm",
      Cantidad = 8,
      Medida = "UN",
      PrecioUnitario = 15,
      Total = 120,
    },
    new()
    {
      Descripcion = "Cartel vinil adhesivo 15 cm x 21 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 2.5,
      Total = 50,
    },
    new()
    {
      Descripcion = "Cartel vinil adhesivo 25 cm x 10 cm",
      Cantidad = 22,
      Medida = "UN",
      PrecioUnitario = 3.5,
      Total = 77,
    },
    new()
    {
      Descripcion = "Cartel vinil adhesivo 20 cm x 20 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 4,
      Total = 80,
    },
    new()
    {
      Descripcion = "Cartel vinil adhesivo A4",
      Cantidad = 12,
      Medida = "UN",
      PrecioUnitario = 5,
      Total = 60,
    },
    new()
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 20 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 9,
      Total = 180,
    },
  ];

  private static void Main(string[] args)
  {
    var service = new ProformaService();

    Console.WriteLine("Generacion de Proformas");
    Console.WriteLine("=======================");
    Console.WriteLine("Ruta de Platilla: {0}", service.RutaPlantilla);
    Console.WriteLine("Ruta de Salida  : {0}", service.RutaSalida);
    Console.WriteLine();
    Console.WriteLine("Procesando...");

    var namefile = service.GenerateExcel(_id, _cliente, _products, _condiciones);

    if (namefile is null)
    {
      Console.WriteLine("Error al generar archivo");
      return;
    }

    Console.WriteLine($"Archivo generado: {namefile}");
  }
}
