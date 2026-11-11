using CommunityToolkit.Mvvm.ComponentModel;
using GeneradorProforma.WPF.Services;

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

  public void CleanupState()
  {
    PID = "--";
    Port = "--";
    IsRunning = false;
  }
}

internal partial class ObservableWebService : ObservableService
{
  private ProcessRunner _runner = new();

  public ObservableWebService()
  {
    _runner.OnLogReceived += (msg) =>
      App.Current.Dispatcher.Invoke(() => Logger(msg));
    _runner.OnStarted += (pid) =>
    {
      PID = pid;
    };
  }

  public async Task Toggle()
  {
    if (!IsRunning)
    {
      IsRunning = true;

      await _runner.StartAsync(
        "cmd.exe",
        "/k \"cd ../output/backend-web && php artisan serve\""
      );

      CleanupState();
    }
    else
    {
      _runner.Stop();

      CleanupState();
    }
  }

  private void Logger(string message)
  {
    Log += $"{message}\n";
  }
}

internal partial class ObservableGeneratorService : ObservableService
{
  private ProcessRunner _runner = new();

  public ObservableGeneratorService()
  {
    _runner.OnLogReceived += (msg) =>
      App.Current.Dispatcher.Invoke(() => Logger(msg));
    _runner.OnStarted += (pid) =>
    {
      PID = pid;
    };
  }

  public async Task Toggle()
  {
    if (!IsRunning)
    {
      IsRunning = true;

      await _runner.StartAsync("pwd", "");

      CleanupState();
    }
    else
    {
      _runner.Stop();

      CleanupState();
    }
  }

  private void Logger(string message)
  {
    Log += $"{message}\n";
  }
}
