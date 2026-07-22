Add-Type -AssemblyName System.Drawing
$img = [System.Drawing.Image]::FromFile('C:\xampp\htdocs\old age home management\Old-Age-Home-Management-System\assets\images\traditional-pattern.png')
$img.RotateFlip('Rotate270FlipNone')
$img.Save('C:\xampp\htdocs\old age home management\Old-Age-Home-Management-System\assets\images\traditional-pattern-horiz.png', [System.Drawing.Imaging.ImageFormat]::Png)
$img.Dispose()
