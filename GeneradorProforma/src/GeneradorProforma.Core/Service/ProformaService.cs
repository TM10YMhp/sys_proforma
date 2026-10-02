using GeneradorProforma.Core.Entity;
using OfficeOpenXml;
using Spire.Xls;

namespace GeneradorProforma.Core.Service;

public interface IProformaService
{
  string RutaPlantilla { get; }
  string RutaSalida { get; }
  string? GenerateExcel(Proforma proforma);

  string? GeneratePDF(string namefile);
}

public class ProformaService : IProformaService
{
  public string RutaPlantilla { get; } =
    Path.Combine(AppContext.BaseDirectory, "Plantillas");
  public string RutaSalida { get; } =
    Path.Combine(AppContext.BaseDirectory, "ArchivosProcesados");

  public ProformaService()
  {
    ExcelPackage.License.SetNonCommercialPersonal("<Your Name>");

    Directory.CreateDirectory(RutaPlantilla);
    Directory.CreateDirectory(RutaSalida);
  }

  private static void FillWithProducts(
    ExcelWorksheet sheet,
    List<Producto> productos
  )
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
    foreach (var item in productos)
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

  private static void FillWithId(ExcelWorksheet sheet, string id)
  {
    sheet.Cells["A2"].Value = $"PROFORMA {id}";
  }

  private static void FillWithClient(ExcelWorksheet sheet, ClienteInfo cliente)
  {
    sheet.Cells["A3"].Value = $"SEÑORES: {cliente.Empresa}";
    sheet.Cells["A4"].Value = $"ATENCIÓN: {cliente.NombreCliente}";
    sheet.Cells["A5"].Value = $"COND. PAGO: {cliente.CondicionPago}";
  }

  private static void FillWithConditions(
    ExcelWorksheet sheet,
    CondicionesInfo condiciones
  )
  {
    sheet.Cells["B41"].Value =
      $"TIEMPO DE FABRICACIÓN: {condiciones.TiempoFabricacion}";
    sheet.Cells["B42"].Value =
      $"VALIDEZ DE LA OFERTA: {condiciones.ValidezOferta}";
  }

  public string? GenerateExcel(Proforma proforma)
  {
    var rutaArchivoPlantilla = Path.Combine(RutaPlantilla, "plantilla.xlsx");
    if (!File.Exists(rutaArchivoPlantilla))
    {
      Console.WriteLine("Plantilla no encontrada");
      return null;
    }

    // TODO: deberia evitar duplicados o ser unicos?
    var extension = Path.GetExtension(rutaArchivoPlantilla).ToLowerInvariant();
    var nombreUnico = $"{Guid.NewGuid()}{extension}";
    var rutaArchivoSalida = Path.Combine(RutaSalida, nombreUnico);

    using (var package = new ExcelPackage(rutaArchivoPlantilla))
    {
      ExcelWorksheet sheet = package.Workbook.Worksheets.First();

      FillWithId(sheet, proforma.Id);
      FillWithClient(sheet, proforma.Cliente);
      FillWithProducts(sheet, proforma.Productos);
      FillWithConditions(sheet, proforma.Condiciones);

      package.SaveAs(rutaArchivoSalida);
    }

    return nombreUnico;
  }

  public string? GeneratePDF(string namefile)
  {
    var rutaArchivoExcel = Path.Combine(RutaSalida, namefile);
    if (!File.Exists(rutaArchivoExcel))
    {
      Console.WriteLine("Archivo no encontrado:");
      Console.WriteLine(rutaArchivoExcel);
      return null;
    }

    var nombreArchivo = Path.GetFileNameWithoutExtension(namefile);
    var filename = $"{nombreArchivo}.pdf";
    var rutaArchivoSalida = Path.Combine(RutaSalida, filename);

    Workbook workbook = new();
    workbook.LoadFromFile(rutaArchivoExcel);
    workbook.SaveToFile(rutaArchivoSalida, FileFormat.PDF);

    return rutaArchivoSalida;
  }
}
