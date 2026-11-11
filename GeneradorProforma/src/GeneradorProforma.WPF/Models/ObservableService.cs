using CommunityToolkit.Mvvm.ComponentModel;
using GeneradorProforma.WPF.Services;

namespace GeneradorProforma.WPF.Models;

internal partial class ObservableService : ObservableObject
{
  protected ProcessRunner Runner { get; } = new();

  public ObservableService()
  {
    Runner.OnLogReceived += (msg) =>
      App.Current.Dispatcher.Invoke(() => Logger(msg));
    Runner.OnStarted += (pid) =>
    {
      PID = pid;
    };
  }

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

  protected void Logger(string message)
  {
    Log += $"{message}\n";
  }
}
