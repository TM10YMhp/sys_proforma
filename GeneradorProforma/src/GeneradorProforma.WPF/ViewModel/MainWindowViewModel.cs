using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using GeneradorProforma.WPF.Models;

namespace GeneradorProforma.WPF.ViewModel;

internal partial class MainWindowViewModel : ObservableObject
{
  [ObservableProperty]
  private ObservableWebService _webService = new();

  [RelayCommand]
  private void ToggleWebService()
  {
    WebService.Toggle();
  }
}
