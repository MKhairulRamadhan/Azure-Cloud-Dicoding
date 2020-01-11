<?php

require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=khairulwebapp;AccountKey=xYBjmrYjkfJrG7/oluBPAt6SuFScMX7YIzmfHQ8xTLw6Fwk/XTwAqtowTsieDJN3wtT8/PlFTufTUQzvI93W3Q==";
$containerName = "blockblobs";
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");

	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: submit2.php");
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$data = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Image Analyze</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

	<div class="container">
		<h1>Image Analyzer</h1>
		<p>Choose image</p>

		<form action="submit2.php" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="exampleInput">Choose Image</label>
						<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="" class="form-control">
						<small class="form-text text-muted">We'll never share your image with anyone else.</small>
					</div>
				</div>
				<div class="col-md-6">
					<input type="submit" name="submit" class="btn btn-primary" value="Upload"></input>
				</div>
			</div>
		</form>

		<br>
		<br>
		<table class='table'>
			<thead>
				<tr>
					<th>no</th>
					<th>Name</th>
					<th>URL</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$nomor = 0;
					do {
						foreach ($data->getBlobs() as $blob)
						{
				?>
						<tr>
							<td><?php echo $nomor++ ?></td>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="vision.php" method="post">
									<input type="hidden" name="data" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Analyze" class="btn btn-success">
								</form>
							</td>
						</tr>
				<?php
						}
						$listBlobsOptions->setContinuationToken($data->getContinuationToken());
					} while($data->getContinuationToken());
				?>
			</tbody>
		</table>

	</div>
</body>
</html>