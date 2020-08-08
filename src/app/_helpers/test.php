<!DOCTYPE html>
<html>
<body>

<form action="upload.php" method="post" enctype="multipart/form-data">
  Select image to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="text" name="projectType" id="projectType">
  <input type="text" name="customerName" id="customerName">
  <input type="text" name="company" id="company">
  <input type="text" name="zipcode" id="zipcode">
  <input type="text" name="email" id="email">
  <input type="text" name="phone" id="phone">
  <input type="text" name="notes" id="notes">
  <input type="submit" value="Upload Image" name="submit">
</form>


<form action="upload.php" method="post" enctype="multipart/form-data">
  <input type="submit" value="Upload Image" name="submit">
</form>
</body>
</html>