using OfficeOpenXml;

internal class ClienteInfo
{
  public string Empresa { get; set; } = string.Empty;
  public string NombreCliente { get; set; } = string.Empty;
  public string CondicionPago { get; set; } = string.Empty;
}

internal class CondicionesInfo
{
  public string TiempoFabricacion { get; set; } = string.Empty;
  public string ValidezOferta { get; set; } = string.Empty;
}

internal class Producto
{
  public string Descripcion { get; set; } = string.Empty;
  public int Cantidad { get; set; }
  public string Medida { get; set; } = string.Empty;
  public double PrecioUnitario { get; set; }
  public double Total { get; set; }
}

internal class Program
{
  private readonly string Id = "001 - 2547";

  private readonly ClienteInfo Cliente = new()
  {
    Empresa = "Safresco Peru SAC",
    NombreCliente = "Paul Sanchez",
    CondicionPago = "Credito 15 dias",
  };

  private readonly CondicionesInfo Condiciones = new()
  {
    TiempoFabricacion = "2 dias",
    ValidezOferta = "7 dias",
  };

  private readonly List<Producto> Products =
  [
    new Producto
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 22 cm",
      Cantidad = 27,
      Medida = "UN",
      PrecioUnitario = 10,
      Total = 270,
    },
    new Producto
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 60 cm x 30 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 24,
      Total = 480,
    },
    new Producto
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 30 cm",
      Cantidad = 8,
      Medida = "UN",
      PrecioUnitario = 12.5,
      Total = 100,
    },
    new Producto
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 25 cm",
      Cantidad = 2,
      Medida = "UN",
      PrecioUnitario = 10,
      Total = 20,
    },
    new Producto
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 100 cm x 60 cm",
      Cantidad = 6,
      Medida = "UN",
      PrecioUnitario = 65,
      Total = 390,
    },
    new Producto
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 40 cm x 30 cm",
      Cantidad = 8,
      Medida = "UN",
      PrecioUnitario = 15,
      Total = 120,
    },
    new Producto
    {
      Descripcion = "Cartel vinil adhesivo 15 cm x 21 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 2.5,
      Total = 50,
    },
    new Producto
    {
      Descripcion = "Cartel vinil adhesivo 25 cm x 10 cm",
      Cantidad = 22,
      Medida = "UN",
      PrecioUnitario = 3.5,
      Total = 77,
    },
    new Producto
    {
      Descripcion = "Cartel vinil adhesivo 20 cm x 20 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 4,
      Total = 80,
    },
    new Producto
    {
      Descripcion = "Cartel vinil adhesivo A4",
      Cantidad = 12,
      Medida = "UN",
      PrecioUnitario = 5,
      Total = 60,
    },
    new Producto
    {
      Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 20 cm",
      Cantidad = 20,
      Medida = "UN",
      PrecioUnitario = 9,
      Total = 180,
    },
  ];

  private readonly string _rutaPlantilla = Path.Combine(
    AppContext.BaseDirectory,
    "Plantillas"
  );
  private readonly string _rutaSalida = Path.Combine(
    AppContext.BaseDirectory,
    "ArchivosProcesados"
  );

  public Program()
  {
    Directory.CreateDirectory(_rutaPlantilla);
    Directory.CreateDirectory(_rutaSalida);
  }

  private void FillWithProducts(ExcelWorksheet sheet)
  {
    int filaInicio = 8;
    var celda = new
    {
      Descripcion = "C",
      Cantidad = "D",
      Medida = "E",
      PrecioUnitario = "F",
      Total = "G",
    };
    foreach (var item in Products)
    {
      int numFila = filaInicio++;
      sheet.Cells[$"{celda.Descripcion}{numFila}"].Value = item.Descripcion;
      sheet.Cells[$"{celda.Cantidad}{numFila}"].Value = item.Cantidad;
      sheet.Cells[$"{celda.Medida}{numFila}"].Value = item.Medida;
      sheet.Cells[$"{celda.PrecioUnitario}{numFila}"].Value =
        item.PrecioUnitario;
      sheet.Cells[$"{celda.Total}{numFila}"].Formula =
        $"{celda.Cantidad}{numFila} * {celda.PrecioUnitario}{numFila}";
    }
  }

  private void FillWithId(ExcelWorksheet sheet)
  {
    sheet.Cells["A2"].Value = $"PROFORMA {Id}";
  }

  private void FillWithClient(ExcelWorksheet sheet)
  {
    sheet.Cells["A3"].Value = $"SEÑORES: {Cliente.Empresa}";
    sheet.Cells["A4"].Value = $"ATENCIÓN: {Cliente.NombreCliente}";
    sheet.Cells["A5"].Value = $"COND. PAGO: {Cliente.CondicionPago}";
  }

  private void FillWithConditions(ExcelWorksheet sheet)
  {
    sheet.Cells["B41"].Value =
      $"TIEMPO DE FABRICACIÓN: {Condiciones.TiempoFabricacion}";
    sheet.Cells["B42"].Value =
      $"VALIDEZ DE LA OFERTA: {Condiciones.ValidezOferta}";
  }

  private void CreateExcel()
  {
    ExcelPackage.License.SetNonCommercialPersonal("<Your Name>");

    var rutaArchivoPlantilla = Path.Combine(_rutaPlantilla, "plantilla.xlsx");
    if (!File.Exists(rutaArchivoPlantilla))
    {
      Console.WriteLine("Plantilla no encontrada");
      return;
    }

    // TODO: deberia evitar duplicados o ser unicos?
    var extension = Path.GetExtension(rutaArchivoPlantilla).ToLowerInvariant();
    var nombreUnico = $"{Guid.NewGuid()}{extension}";
    var rutaArchivoSalida = Path.Combine(_rutaSalida, nombreUnico);

    using (var package = new ExcelPackage(rutaArchivoPlantilla))
    {
      ExcelWorksheet sheet = package.Workbook.Worksheets.First();

      FillWithId(sheet);
      FillWithClient(sheet);
      FillWithProducts(sheet);
      FillWithConditions(sheet);

      package.SaveAs(rutaArchivoSalida);
    }
  }

  private static void Main(string[] args)
  {
    var program = new Program();
    Console.WriteLine("Generacion de Proformas");
    Console.WriteLine("=======================");
    Console.WriteLine("Ruta de Platilla: {0}", program._rutaPlantilla);
    Console.WriteLine("Ruta de Salida  : {0}", program._rutaSalida);
    Console.WriteLine();
    Console.WriteLine("Procesando...");
    program.CreateExcel();
    Console.WriteLine("Archivo generado");
  }
}
