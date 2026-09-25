using Microsoft.AspNetCore.Mvc;

namespace GeneradorProforma.WebAPI.Controllers;

[Route("api/[controller]")]
[ApiController]
public class ProformaController : ControllerBase
{
  [HttpGet]
  public IActionResult Generar()
  {
    return Ok("Hola generador!");
  }
}
