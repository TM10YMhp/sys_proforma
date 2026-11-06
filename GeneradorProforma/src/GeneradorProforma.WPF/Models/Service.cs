using System.Diagnostics;
using CommunityToolkit.Mvvm.ComponentModel;

namespace GeneradorProforma.WPF.Models;

internal partial class ObservableService : ObservableObject
{
  [ObservableProperty]
  private string _PID = "--";

  [ObservableProperty]
  private string _port = "--";

  [ObservableProperty]
  private string _log = "";

  [ObservableProperty]
  [NotifyPropertyChangedFor(nameof(BtnText))]
  private bool _isRunning = false;

  public string BtnText => IsRunning ? "Detener" : "Iniciar";

  public Process? Process;

  public void CleanupState()
  {
    PID = "--";
    Port = "--";
    IsRunning = false;
    Process?.Dispose();
    Process = null;
  }
}

internal partial class ObservableWebService : ObservableService
{
  public async Task Start()
  {
    var psi = new ProcessStartInfo
    {
      FileName = "ping",
      Arguments = "google.com -n 5",
      RedirectStandardOutput = true,
      RedirectStandardError = true,
      UseShellExecute = false,
      CreateNoWindow = true,
      StandardOutputEncoding = System.Text.Encoding.UTF8,
    };
    Process = new Process { StartInfo = psi };
    Process.OutputDataReceived += (sender, args) =>
    {
      if (args.Data != null)
      {
        App.Current.Dispatcher.Invoke(() => Logger(args.Data));
      }
    };

    try
    {
      Process.Start();
      Process.BeginOutputReadLine();

      IsRunning = true;
      PID = Process.Id.ToString();

      Logger($"Web ejecutándose. Port: {Port}, PID: {PID}.");

      await Process.WaitForExitAsync();
    }
    catch (Exception ex)
    {
      Logger($"Error al ejecutar: {ex.Message}");
    }
    finally
    {
      // NOTE: esto se ejecuta cuando el proceso termina normalmente o falla
      // no si el usuario lo detiene manualmente
      if (IsRunning)
      {
        CleanupState();
      }
    }
  }

  public void Stop()
  {
    try
    {
      if (Process != null && !Process.HasExited)
      {
        Process.Kill(entireProcessTree: true);
      }
    }
    catch (Exception ex)
    {
      Logger($"Aviso al detener proceso: {ex.Message}");
    }

    CleanupState();
  }

  public async Task Toggle()
  {
    if (!IsRunning)
    {
      await Start();
    }
    else
    {
      Stop();
    }
  }

  private void Logger(string message)
  {
    Log += $"{message}\n";
  }
}
