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

  public void CleanupState()
  {
    PID = "--";
    Port = "--";
    IsRunning = false;
  }
}

internal partial class ObservableWebService : ObservableService
{
  public void Start()
  {
    Logger("Iniciar servicio web");

    IsRunning = true;
    PID = new Random().Next(1000, 9999).ToString();
  }

  public void Stop()
  {
    Logger("Detener servicio web");

    CleanupState();
  }

  public void Toggle()
  {
    if (!IsRunning)
    {
      Start();
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
