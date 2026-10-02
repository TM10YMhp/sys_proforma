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

    return new Proforma
    {
      Id = id,
      Cliente = cliente,
      Productos = products,
      Condiciones = condiciones,
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

    Console.WriteLine(request);

    var namefile = _service.GenerateExcel(
      new Proforma()
      {
        Id = request.Id,
        Cliente = request.Cliente,
        Productos = request.Productos,
        Condiciones = request.Condiciones,
      }
    );

    if (namefile is null)
    {
      return BadRequest(new { mensaje = "Error al generar archivo" });
    }

    string filePath = Path.Combine(_service.RutaSalida, namefile);
    if (!System.IO.File.Exists(filePath))
    {
      return NotFound(new { mensaje = "Archivo no encontrado" });
    }

    string extension = Path.GetExtension(filePath).ToLowerInvariant();
    string contentType = extension switch
    {
      ".xlsx" =>
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      _ => "application/octet-stream",
    };
    string fileName = Path.GetFileName(filePath);

    return PhysicalFile(
      filePath,
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

    var rutaSalidaPdf = _service.GeneratePDF(
      new Proforma()
      {
        Id = request.Id,
        Cliente = request.Cliente,
        Productos = request.Productos,
        Condiciones = request.Condiciones,
      }
    );
    if (rutaSalidaPdf is null)
    {
      return BadRequest(new { mensaje = "Error al generar archivo" });
    }

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
}
