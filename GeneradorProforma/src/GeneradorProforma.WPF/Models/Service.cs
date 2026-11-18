namespace GeneradorProforma.WPF.Models;

internal partial class ObservableWebService : ObservableService
{
  public async Task Toggle()
  {
    if (!IsRunning)
    {
      IsRunning = true;
      await Runner.StartAsync(
        "cmd.exe",
        "/k \"cd ../output/backend-web && php artisan serve\""
      );
      CleanupState();
    }
    else
    {
      Runner.Stop();
      CleanupState();
    }
  }
}

internal partial class ObservableGeneratorService : ObservableService
{
  public async Task Toggle()
  {
    if (!IsRunning)
    {
      IsRunning = true;
      await Runner.StartAsync(
        "cmd.exe",
        "/k \"cd ../output/GeneradorProforma && GeneradorProforma.WebAPI.exe\""
      );
      CleanupState();
    }
    else
    {
      Runner.Stop();
      CleanupState();
    }
  }
}

internal partial class ObservableTunelService : ObservableService
{
  public async Task Toggle()
  {
    if (!IsRunning)
    {
      IsRunning = true;
      // TODO: solo usar en produccion `-n public:sysproforma`
      await Runner.StartAsync(
        "cmd.exe",
        "/k \"cd ../output && zrok2 share public 8000 --headless\""
      );
      CleanupState();
    }
    else
    {
      Runner.Stop();
      CleanupState();
    }
  }
}
