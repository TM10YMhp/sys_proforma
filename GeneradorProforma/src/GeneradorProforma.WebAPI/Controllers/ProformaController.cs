using GeneradorProforma.Core.Entity;
using GeneradorProforma.Core.Service;
using Microsoft.AspNetCore.Mvc;

namespace GeneradorProforma.WebAPI.Controllers;

[Route("api/[controller]")]
[ApiController]
public class ProformaController(IProformaService service) : ControllerBase
{
  private readonly IProformaService _service = service;

  private static Proforma GetDatosPrueba()
  {
    const string id = "001 - 2547";

    ClienteInfo cliente = new()
    {
      Empresa = "Safresco Peru SAC",
      NombreCliente = "Paul Sanchez",
      CondicionPago = "Credito 15 dias",
    };

    CondicionesInfo condiciones = new()
    {
      TiempoFabricacion = "2 dias",
      ValidezOferta = "7 dias",
    };

    List<Producto> products =
    [
      new()
      {
        Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 22 cm",
        Cantidad = 27,
        Medida = "UN",
        PrecioUnitario = 10,
      },
      new()
      {
        Descripcion = "Cartel vinil en base celtex 3 mm de 60 cm x 30 cm",
        Cantidad = 20,
        Medida = "UN",
        PrecioUnitario = 24,
      },
      new()
      {
        Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 30 cm",
        Cantidad = 8,
        Medida = "UN",
        PrecioUnitario = 12.5,
      },
      new()
      {
        Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 25 cm",
        Cantidad = 2,
        Medida = "UN",
        PrecioUnitario = 10,
      },
      new()
      {
        Descripcion = "Cartel vinil en base celtex 3 mm de 100 cm x 60 cm",
        Cantidad = 6,
        Medida = "UN",
        PrecioUnitario = 65,
      },
      new()
      {
        Descripcion = "Cartel vinil en base celtex 3 mm de 40 cm x 30 cm",
        Cantidad = 8,
        Medida = "UN",
        PrecioUnitario = 15,
      },
      new()
      {
        Descripcion = "Cartel vinil adhesivo 15 cm x 21 cm",
        Cantidad = 20,
        Medida = "UN",
        PrecioUnitario = 2.5,
      },
      new()
      {
        Descripcion = "Cartel vinil adhesivo 25 cm x 10 cm",
        Cantidad = 22,
        Medida = "UN",
        PrecioUnitario = 3.5,
      },
      new()
      {
        Descripcion = "Cartel vinil adhesivo 20 cm x 20 cm",
        Cantidad = 20,
        Medida = "UN",
        PrecioUnitario = 4,
      },
      new()
      {
        Descripcion = "Cartel vinil adhesivo A4",
        Cantidad = 12,
        Medida = "UN",
        PrecioUnitario = 5,
      },
      new()
      {
        Descripcion = "Cartel vinil en base celtex 3 mm de 30 cm x 20 cm",
        Cantidad = 20,
        Medida = "UN",
        PrecioUnitario = 9,
      },
    ];

    return new Proforma
    {
      Id = id,
      Cliente = cliente,
      Productos = products,
      Condiciones = condiciones,
      FechaEmision = DateTimeOffset.Now,
      FechaVencimiento = DateTimeOffset.Now.AddDays(7),
    };
  }

  [HttpPost("excel")]
  public IActionResult GenerarExcel([FromBody] Proforma? request)
  {
    // NOTE: prueba
    // request = GetDatosPrueba();

    if (request is null)
    {
      return BadRequest(
        new { mensaje = "El cuerpo de la solicitud no puede estar vacio." }
      );
    }

    var result = _service.GenerateExcel(request);

    if (result.IsError)
    {
      return BadRequest(result.Errors);
    }

    var rutaSalidaExcel = result.Value;
    if (!System.IO.File.Exists(rutaSalidaExcel))
    {
      return NotFound(new { mensaje = "Archivo no encontrado" });
    }

    string extension = Path.GetExtension(rutaSalidaExcel).ToLowerInvariant();
    string contentType = extension switch
    {
      ".xlsx" =>
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      _ => "application/octet-stream",
    };
    string fileName = Path.GetFileName(rutaSalidaExcel);

    return PhysicalFile(
      rutaSalidaExcel,
      contentType,
      fileName,
      enableRangeProcessing: true
    );
  }

  [HttpPost("pdf")]
  public IActionResult GenerarPDF([FromBody] Proforma? request)
  {
    // request = GetDatosPrueba();

    if (request is null)
    {
      return BadRequest(
        new { mensaje = "El cuerpo de la solicitud no puede estar vacio." }
      );
    }

    var result = _service.GeneratePDF(request);
    if (result.IsError)
    {
      return BadRequest(result.Errors);
    }

    var rutaSalidaPdf = result.Value;
    string extension = Path.GetExtension(rutaSalidaPdf).ToLowerInvariant();
    string contentType = extension switch
    {
      ".pdf" => "application/pdf",
      _ => "application/octet-stream",
    };
    string fileName = Path.GetFileName(rutaSalidaPdf);

    return PhysicalFile(
      rutaSalidaPdf,
      contentType,
      fileName,
      enableRangeProcessing: true
    );
  }

  [HttpPost("img")]
  public IActionResult GenerarImg([FromBody] Proforma? request)
  {
    // request = GetDatosPrueba();

    if (request is null)
    {
      return BadRequest(
        new { mensaje = "El cuerpo de la solicitud no puede estar vacio." }
      );
    }

    var result = _service.GenerateImg(request);
    if (result.IsError)
    {
      return BadRequest(result.Errors);
    }

    var rutaSalidaImg = result.Value;
    return PhysicalFile(rutaSalidaImg, "image/webp");
  }

  [HttpGet("excel/{id}")]
  public IActionResult GenerarExcelPorId([FromRoute] string id)
  {
    var result = _service.GetExcelById(id);
    if (result.IsError)
    {
      return NotFound(result.Errors);
    }

    var rutaArchivoExcel = result.Value;
    string extension = Path.GetExtension(rutaArchivoExcel).ToLowerInvariant();
    string contentType = extension switch
    {
      ".xlsx" =>
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      _ => "application/octet-stream",
    };
    string fileName = Path.GetFileName(rutaArchivoExcel);

    return PhysicalFile(
      rutaArchivoExcel,
      contentType,
      fileName,
      enableRangeProcessing: true
    );
  }

  [HttpGet("img/{id}")]
  public IActionResult GenerarImgPorId([FromRoute] string id)
  {
    var result = _service.GetImgById(id);
    if (result.IsError)
    {
      return NotFound(result.Errors);
    }

    var rutaImagen = result.Value;
    // string fileName = Path.GetFileName(rutaImagen);

    return PhysicalFile(rutaImagen, "image/webp");
  }
}
