using ErrorOr;
using GeneradorProforma.Core.Entity;
using OfficeOpenXml;
using PhotoSauce.MagicScaler;
using PhotoSauce.NativeCodecs.Libwebp;
using Spire.Xls;

namespace GeneradorProforma.Core.Service;

public interface IProformaService
{
  string RutaPlantilla { get; }
  string RutaSalida { get; }

  ErrorOr<string> GenerateExcel(Proforma proforma);
  ErrorOr<string> GeneratePDF(Proforma proforma);
  ErrorOr<string> GenerateImg(Proforma proforma);
  ErrorOr<string> GetExcelById(string id);
  ErrorOr<string> GetImgById(string id);
}

public class ProformaService : IProformaService
{
  public string RutaPlantilla { get; } =
    Path.Combine(AppContext.BaseDirectory, "Plantillas");
  public string RutaSalida { get; } =
    Path.Combine(AppContext.BaseDirectory, "ArchivosProcesados");

  public ProformaService()
  {
    ExcelPackage.License.SetNonCommercialPersonal("Focus Digital");

    Directory.CreateDirectory(RutaPlantilla);
    Directory.CreateDirectory(RutaSalida);

    CodecManager.Configure(codecs =>
    {
      codecs.UseLibwebp();
    });
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

  public ErrorOr<string> GenerateExcel(Proforma proforma)
  {
    var nombrePlantilla = "plantilla.xlsx";

    if (Path.GetExtension(nombrePlantilla).ToLowerInvariant() != ".xlsx")
    {
      return Error.Unexpected(
        description: "Plantilla debe ser un archivo xlsx"
      );
    }

    var rutaArchivoPlantilla = Path.Combine(RutaPlantilla, nombrePlantilla);
    if (!File.Exists(rutaArchivoPlantilla))
    {
      return Error.Unexpected(description: "Plantilla no encontrada");
    }

    var id = proforma.Id.Trim().Replace(" ", "_");
    var nombreSalida = $"proforma-{id}.xlsx";
    var rutaArchivoSalida = Path.Combine(RutaSalida, nombreSalida);

    // if (File.Exists(rutaArchivoSalida))
    // {
    //   return Error.Unexpected(
    //     description: "Ya existe un archivo con el mismo nombre"
    //   );
    // }

    using (var package = new ExcelPackage(rutaArchivoPlantilla))
    {
      ExcelWorksheet sheet = package.Workbook.Worksheets.First();

      FillWithId(sheet, proforma.Id);
      FillWithClient(sheet, proforma.Cliente);
      FillWithProducts(sheet, proforma.Productos);
      FillWithConditions(sheet, proforma.Condiciones);

      package.SaveAs(rutaArchivoSalida);
    }

    return rutaArchivoSalida;
  }

  public ErrorOr<string> GeneratePDF(Proforma proforma)
  {
    var result = GenerateExcel(proforma);
    if (result.IsError)
    {
      return Error.Unexpected(description: "Error al generar archivo");
    }

    var rutaSalidaExcel = result.Value;
    if (!File.Exists(rutaSalidaExcel))
    {
      return Error.Unexpected(description: "Archivo no encontrado");
    }

    var nombreArchivo = Path.GetFileNameWithoutExtension(rutaSalidaExcel);
    var filename = $"{nombreArchivo}.pdf";
    var rutaArchivoSalida = Path.Combine(RutaSalida, filename);

    Workbook workbook = new();
    workbook.LoadFromFile(rutaSalidaExcel);
    workbook.SaveToFile(rutaArchivoSalida, FileFormat.PDF);

    return rutaArchivoSalida;
  }

  public ErrorOr<string> GenerateImg(Proforma proforma)
  {
    var result = GeneratePDF(proforma);
    if (result.IsError)
    {
      return Error.Unexpected(description: "Error al generar archivo");
    }

    var rutaSalidaPdf = result.Value;
    if (!File.Exists(rutaSalidaPdf))
    {
      return Error.Unexpected(description: "Archivo no encontrado");
    }

    var nombreArchivo = Path.GetFileNameWithoutExtension(rutaSalidaPdf);
    var filename = $"{nombreArchivo}.webp";
    var rutaArchivoSalida = Path.Combine(RutaSalida, filename);
    var rutaArchivoSalidaTemporal = Path.Combine(RutaSalida, filename + ".tmp");

    var pdf = File.OpenRead(rutaSalidaPdf);

#pragma warning disable CA1416
    PDFtoImage.Conversion.SaveWebp(
      imageFilename: rutaArchivoSalida,
      pdfStream: pdf,
      page: 0,
      options: new PDFtoImage.RenderOptions
      {
        Dpi = 150,
        AntiAliasing = PDFtoImage.PdfAntiAliasing.None,
      }
    );
#pragma warning restore CA1416

    var settings = new ProcessImageSettings
    {
      EncoderOptions = new WebpLosslessEncoderOptions(9),
    };
    settings.TrySetEncoderFormat(ImageMimeTypes.Webp);

    MagicImageProcessor.ProcessImage(
      rutaArchivoSalida,
      rutaArchivoSalidaTemporal,
      settings
    );

    File.Replace(rutaArchivoSalidaTemporal, rutaArchivoSalida, null);

    return rutaArchivoSalida;
  }

  public ErrorOr<string> GetExcelById(string id)
  {
    var ruta = Path.Combine(RutaSalida, $"proforma-{id}.xlsx");
    if (!File.Exists(ruta))
    {
      return Error.Unexpected(description: "Archivo no encontrado");
    }

    return ruta;
  }

  public ErrorOr<string> GetImgById(string id)
  {
    var filename = $"proforma-{id}.webp";
    var ruta = Path.Combine(RutaSalida, filename);
    if (!File.Exists(ruta))
    {
      return Error.Unexpected(
        description: "Archivo no encontrado",
        metadata: new() { { "filename", filename } }
      );
    }

    return ruta;
  }
}
