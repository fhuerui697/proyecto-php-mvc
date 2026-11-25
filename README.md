# PHP + MySQL + GitHub Actions + Alwaysdata

## 1. Local (Windows / PowerShell)

Descomprime el ZIP en una carpeta de trabajo fuera de `htdocs` y abre esa carpeta en VS Code.
Arranca MySQL en XAMPP y crea una base llamada `prueba` (por ejemplo, desde phpMyAdmin).

```powershell
Copy-Item config.example.php config.php
php init.php
php -S localhost:8000 -t public
```

Si `php` no está en PATH, usa `C:\xampp\php\php.exe` en su lugar. Ajusta `config.php` si tu MySQL local tiene otras credenciales. Abre http://localhost:8000.

## 2. Alwaysdata (una sola vez)

En Databases > MySQL, crea la base `prueba` (quedará como `fede_prueba` en la cuenta de ejemplo) y asigna un usuario con permisos sobre ella.

Desde PowerShell, fuera del repositorio, genera una clave dedicada:

```powershell
ssh-keygen -t ed25519 -C "github-actions" -f github_alwaysdata
```

Deja vacía la frase de contraseña para la automatización. Añade el contenido de `github_alwaysdata.pub` en Remote access > SSH/SFTP > SSH Keys. Prueba:

```powershell
ssh -i github_alwaysdata fede@ssh-fede.alwaysdata.net
```

Comprueba la huella del servidor en el panel antes de aceptarla. Sustituye `fede` por tu cuenta. Esta plantilla presupone que el usuario SSH es el usuario principal de la cuenta.

Ya dentro de SSH:

```bash
mkdir -p /home/fede/proyecto/public
nano /home/fede/proyecto/config.php
```

Escribe el contenido siguiente con las credenciales reales (no lo subas a GitHub):

```php
<?php
$host = 'mysql-fede.alwaysdata.net';
$bd = 'fede_prueba';
$user = 'USUARIO_MYSQL';
$pass = 'CONTRASEÑA_MYSQL';
```

Guarda con Ctrl+O, Enter y sal con Ctrl+X. Luego:

```bash
chmod 600 /home/fede/proyecto/config.php
exit
```

En Web > Sites, crea el sitio PHP con la dirección `fede.alwaysdata.net/proyecto`. Si ya existe, edita únicamente ese sitio. En Root directory, si el panel muestra el prefijo `/home/fede/`, escribe `proyecto/public/`. No uses `www/` ni cambies la raíz de otras webs. El directorio completo es `/home/fede/proyecto/public/`.

## 3. GitHub (una sola vez)

Crea un repositorio público vacío (sin README). Antes del primer push, entra en Settings > Secrets and variables > Actions y crea cuatro Repository secrets:

| Nombre | Valor de ejemplo |
|---|---|
| SSH_HOST | ssh-fede.alwaysdata.net |
| SSH_USER | fede |
| SSH_KEY | contenido completo del archivo github_alwaysdata (privado) |
| SSH_KNOWN_HOSTS | entrada verificada del servidor en known_hosts |

En PowerShell, desde la carpeta de las claves:

```powershell
Get-Content github_alwaysdata -Raw | Set-Clipboard
```

Para copiar la entrada de la huella ya verificada:

```powershell
ssh-keygen -F ssh-fede.alwaysdata.net | Where-Object { $_ -notmatch '^#' } | Set-Clipboard
```

Pega los valores en sus secretos correspondientes. No los subas como archivos al repositorio. No se necesita DB_CONFIG.

Desde la raíz del proyecto en local:

```powershell
git init -b main
git add .
git status
git commit -m "Primera práctica"
git remote add origin https://github.com/TU_USUARIO/TU_REPOSITORIO.git
git push -u origin main
```

Comprueba en `git status` que aparecen `.github/workflows/deploy.yml` y `.gitignore`, pero no `config.php` ni claves. Si reutilizas el repositorio anterior, conserva los cuatro secretos y sustituye los archivos antiguos en una copia clonada; no hagas `git init` sobre ella.

En GitHub > Actions comprueba el resultado. Después abre https://fede.alwaysdata.net/proyecto/ (adaptado a tu cuenta).

## 4. Uso habitual

```powershell
git add .
git commit -m "Actualizar práctica"
git push
```

El despliegue copia `public/`, `conexion.php`, `init.php` y `database.sql`. Si creas nuevos archivos PHP auxiliares fuera de `public/`, añádelos expresamente a la línea rsync. No copia ni borra `config.php`; tampoco elimina archivos antiguos del servidor. Cada alumno debe utilizar su propia cuenta de Alwaysdata.

`database.sql` crea la tabla si no existe. Los datos ya guardados se conservan. Los cambios de estructura posteriores necesitan migraciones SQL; no se sincronizan automáticamente los registros locales y remotos. No uses `DROP TABLE` ni `TRUNCATE` en el script automático.

Si venías del ejemplo anterior con `www/www`, elimina el antiguo `config.json` de esa ruta y cambia la contraseña de ese usuario MySQL por precaución. No borres otros sitios ni sus datos.
