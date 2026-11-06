using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace GeneradorProforma.WPF.ViewModel;

internal partial class MainWindowViewModel : ObservableObject
{
  [ObservableProperty]
  private string _logService = "";

  private bool _isRunning = false;

  [RelayCommand]
  private void ToggleService()
  {
    if (!_isRunning)
    {
      _isRunning = true;
      Log("Iniciar");
    }
    else
    {
      _isRunning = false;
      Log("Terminar");
    }
  }

  private void Log(string message)
  {
    LogService += $"{message}\n";
  }
}
