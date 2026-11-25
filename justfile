set default-list := true

output_dir  := "sys_proforma"
backend_dir := output_dir + "/backend-web"

set_db:
  cp backend-web/database/database.sqlite {{backend_dir}}/database/database.sqlite

# Elimina builds anteriores
@clean:
  echo "🗑️ Limpiando builds anteriores..."
  rm -rf {{output_dir}}/
  echo

# Ejecuta todo el proceso de compilación
[script]
build: clean
  echo "[*] 🚀 Iniciando exportación limpia del proyecto..."
  git checkout-index -a --prefix={{output_dir}}/
  echo

  echo "[*] 🧹 Limpiando exportación..."
  cd {{output_dir}}

  mv scripts/sys_proforma.cmd sys_proforma.cmd

  rm -rf GeneradorProforma/
  rm -rf scripts/
  rm .gitignore
  rm justfile
  rm README.md

  cd ..
  echo


  echo "[web] ⚡ Instalando y compilando Frontend..."
  cd backend-web
  npm install --loglevel info
  npm run build
  cd ..
  cp -r backend-web/public/build {{backend_dir}}/public/build
  echo

  echo "[web] 📄 Configurando entorno de Laravel (.env)..."
  cd {{backend_dir}}
  cp .env.example .env
  sed -i "s|^GENERADOR_PROFORMA_URL=.*|GENERADOR_PROFORMA_URL=http://localhost:5000|" .env
  sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
  sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
  sed -i "s|^APP_URL=.*|APP_URL=https://sysproforma.shares.zrok.io|" .env
  echo

  echo "[web] 📦 Instalando dependencias de PHP..."
  composer install --no-dev --optimize-autoloader
  echo

  echo "[web] 🔑 Generando App Key de Laravel..."
  php artisan key:generate
  echo

  echo "[web] 🗄️ Ejecutando migraciones de Base de Datos..."
  php artisan migrate --force
  echo

  cd ../..


  echo "[generador] ⚙️ Compilando Web API y WPF para Windows (Release)..."
  cd GeneradorProforma
  dotnet publish src/GeneradorProforma.WebAPI -c Release -r win-x64 -o publish
  dotnet publish src/GeneradorProforma.WPF -c Release -r win-x64 -o publish_wpf \
    --no-self-contained -p:DebugType=None -p:PublishSingleFile=true
  cd ..
  echo

  echo "[generador] 📦 Moviendo artefactos de C#..."
  mv GeneradorProforma/publish {{output_dir}}/GeneradorProforma
  mv GeneradorProforma/publish_wpf/GeneradorProforma.WPF.exe {{output_dir}}/
  rm -r GeneradorProforma/publish_wpf/
  echo


  echo "========================================================="
  echo "✅ ¡Build completado con éxito!"
  echo "========================================================="
  echo "👉 Web API de C# lista en:  {{output_dir}}/GeneradorProforma"
  echo "👉 Para probar Laravel:     cd {{backend_dir}} && php artisan serve"
