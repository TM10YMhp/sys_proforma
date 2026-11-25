namespace GeneradorProforma.WPF.Models;

internal partial class ObservableWebService : ObservableService
{
  public async Task Toggle()
  {
    if (!IsRunning)
    {
      IsRunning = true;

#if DEBUG
      string arguments =
        "/k \"cd ../sys_proforma/backend-web && php artisan serve\"";
#else
      string arguments = "/k \"cd backend-web && php artisan serve\"";
#endif

      await Runner.StartAsync("cmd.exe", arguments);
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

#if DEBUG
      string arguments =
        "/k \"cd ../sys_proforma/GeneradorProforma && GeneradorProforma.WebAPI.exe\"";
#else
      string arguments =
        "/k \"cd GeneradorProforma && GeneradorProforma.WebAPI.exe\"";
#endif

      await Runner.StartAsync("cmd.exe", arguments);
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

#if DEBUG
      string arguments =
        "/k \"cd ../sys_proforma && zrok2 share public 8000 --headless\"";
#else
      string arguments =
        "/k \"zrok2 share public 8000 -n public:sysproforma --headless\"";
#endif

      await Runner.StartAsync("cmd.exe", arguments);
      CleanupState();
    }
    else
    {
      Runner.Stop();
      CleanupState();
    }
  }
}
