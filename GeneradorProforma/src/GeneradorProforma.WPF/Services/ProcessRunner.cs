using System.Diagnostics;

namespace GeneradorProforma.WPF.Services;

internal class ProcessRunner
{
  private Process? _process;
  private bool _isRunning;

  public event Action<string>? OnLogReceived;
  public event Action<string>? OnStarted;
  public event Action? OnExited;

  public bool IsRunning => _isRunning;

  public async Task StartAsync(string fileName, string arguments)
  {
    if (_isRunning)
      return;

    var psi = new ProcessStartInfo
    {
      FileName = fileName,
      Arguments = arguments,
      RedirectStandardOutput = true,
      RedirectStandardError = true,
      UseShellExecute = false,
      CreateNoWindow = true,
      StandardOutputEncoding = System.Text.Encoding.UTF8,
    };

    _process = new Process { StartInfo = psi };

    _process.OutputDataReceived += (sender, args) =>
    {
      if (args.Data != null)
        OnLogReceived?.Invoke(args.Data);
    };

    // NOTE: ciertos comandos como cmd mandan las salidas de error
    _process.ErrorDataReceived += (sender, args) =>
    {
      if (args.Data != null)
        OnLogReceived?.Invoke($"[ERROR] {args.Data}");
    };

    try
    {
      _process.Start();
      _process.BeginOutputReadLine();
      _process.BeginErrorReadLine();

      _isRunning = true;
      OnStarted?.Invoke(_process.Id.ToString());

      await _process.WaitForExitAsync();
    }
    catch (Exception ex)
    {
      OnLogReceived?.Invoke($"Error al ejecutar el proceso: {ex.Message}");
    }
    finally
    {
      // NOTE: esto se ejecuta cuando el proceso termina normalmente o falla
      // no si el usuario lo detiene manualmente
      if (_isRunning)
      {
        _isRunning = false;
        _process?.Dispose();
        _process = null;

        OnExited?.Invoke();
      }
    }
  }

  public void Stop()
  {
    if (!_isRunning)
      return;

    try
    {
      if (_process != null && !_process.HasExited)
      {
        _process.KillTree();
      }
    }
    catch (Exception ex)
    {
      OnLogReceived?.Invoke($"Aviso al detener proceso: {ex.Message}");
    }
    finally
    {
      _isRunning = false;
      _process?.Dispose();
      _process = null;

      OnExited?.Invoke();
    }
  }
}

// NOTE:
// https://stackoverflow.com/a/45667693
// https://github.com/dotnet/cli/blob/master/test/Microsoft.DotNet.Tools.Tests.Utilities/Extensions/ProcessExtensions.cs
internal static class ProcessExtensions
{
  private static readonly int _defaultMillisecods = 30000;

  public static void KillTree(this Process process)
  {
    process.KillTree(_defaultMillisecods);
  }

  public static void KillTree(this Process process, int millisecods)
  {
    var startInfo = new ProcessStartInfo
    {
      FileName = "taskkill",
      Arguments = $"/F /T /PID {process.Id}",
      CreateNoWindow = true,
      UseShellExecute = false,
    };

    using (var killer = Process.Start(startInfo))
    {
      killer?.WaitForExit(millisecods);
    }
  }
}
