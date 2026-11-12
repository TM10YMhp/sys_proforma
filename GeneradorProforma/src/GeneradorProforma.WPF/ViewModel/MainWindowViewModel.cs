using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using GeneradorProforma.WPF.Models;

namespace GeneradorProforma.WPF.ViewModel;

internal partial class MainWindowViewModel : ObservableObject
{
  [ObservableProperty]
  private ObservableWebService _webService = new();

  [ObservableProperty]
  private ObservableGeneratorService _generatorService = new();

  [RelayCommand(AllowConcurrentExecutions = true)]
  private async Task ToggleWebService()
  {
    await WebService.Toggle();
  }

  [RelayCommand(AllowConcurrentExecutions = true)]
  private async Task ToggleGeneratorService()
  {
    await GeneratorService.Toggle();
  }

  public void StopProcesses()
  {
    try
    {
      WebService.Stop();
      GeneratorService.Stop();
    }
    catch
    { /* Ignorar errores al cerrar */
    }
  }
}
