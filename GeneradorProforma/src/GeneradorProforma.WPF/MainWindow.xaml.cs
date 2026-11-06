using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Threading;

namespace GeneradorProforma.WPF;

/// <summary>
/// Interaction logic for MainWindow.xaml
/// </summary>
public partial class MainWindow : Window
{
  public MainWindow()
  {
    InitializeComponent();
  }

  private void TextBox_TargetUpdated(object sender, DataTransferEventArgs e)
  {
    if (sender is TextBox textBox)
    {
      textBox.ScrollToEnd();
    }
  }

  private void TabControl_SelectionChanged(
    object sender,
    SelectionChangedEventArgs e
  )
  {
    if (e.Source is TabControl tabControl && tabControl.SelectedContent != null)
    {
      if (tabControl.SelectedContent is TextBox textBox)
      {
        // TODO: asegura el scroll pero muestra un parpadeo
        Dispatcher.BeginInvoke(
          new Action(() =>
          {
            textBox.ScrollToEnd();
          }),
          DispatcherPriority.Background
        );
      }
    }
  }
}
