using GeneradorProforma.Core.Entity;
using OfficeOpenXml;

namespace GeneradorProforma.Core.Service;

public interface IProformaService
{
  string? GenerateExcel(
    string id,
    ClienteInfo cliente,
    List<Producto> productos,
    CondicionesInfo condiciones
  );
}

public class ProformaService : IProformaService
{
  public readonly string RutaPlantilla = Path.Combine(
    AppContext.BaseDirectory,
    "Plantillas"
  );
  public readonly string RutaSalida = Path.Combine(
    AppContext.BaseDirectory,
    "ArchivosProcesados"
  );

  public ProformaService()
  {
    Directory.CreateDirectory(RutaPlantilla);
    Directory.CreateDirectory(RutaSalida);
  }

  private void FillWithProducts(ExcelWorksheet sheet, List<Producto> productos)
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

  private void FillWithId(ExcelWorksheet sheet, string id)
  {
    sheet.Cells["A2"].Value = $"PROFORMA {id}";
  }

  private void FillWithClient(ExcelWorksheet sheet, ClienteInfo cliente)
  {
    sheet.Cells["A3"].Value = $"SEÑORES: {cliente.Empresa}";
    sheet.Cells["A4"].Value = $"ATENCIÓN: {cliente.NombreCliente}";
    sheet.Cells["A5"].Value = $"COND. PAGO: {cliente.CondicionPago}";
  }

  private void FillWithConditions(
    ExcelWorksheet sheet,
    CondicionesInfo condiciones
  )
  {
    sheet.Cells["B41"].Value =
      $"TIEMPO DE FABRICACIÓN: {condiciones.TiempoFabricacion}";
    sheet.Cells["B42"].Value =
      $"VALIDEZ DE LA OFERTA: {condiciones.ValidezOferta}";
  }

  public string? GenerateExcel(
    string id,
    ClienteInfo cliente,
    List<Producto> productos,
    CondicionesInfo condiciones
  )
  {
    ExcelPackage.License.SetNonCommercialPersonal("<Your Name>");

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

      FillWithId(sheet, id);
      FillWithClient(sheet, cliente);
      FillWithProducts(sheet, productos);
      FillWithConditions(sheet, condiciones);

      package.SaveAs(rutaArchivoSalida);
    }

    return nombreUnico;
  }
}
