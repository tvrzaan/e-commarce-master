<?php
session_start();
require_once('../autoload.php');
require_once('../includes/functions.php');

use App\Models\Product;

// Check if user is admin
if (!is_admin()) {
    header('Location: ../index.php');
    exit();
}

$productModel = new Product();
$message = '';
$error = '';

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$productId = (int)$_GET['id'];
$product = $productModel->getProductById($productId);

if (!$product) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $productData = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'category' => $_POST['category']
    ];

    // Handle image upload if new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/products/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tempName = $_FILES['image']['tmp_name'];
        $originalName = $_FILES['image']['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($tempName, $targetPath)) {
            $productData['image'] = 'uploads/products/' . $fileName;

            // Delete old image if it exists
            if (isset($product['image']) && file_exists('../' . $product['image'])) {
                unlink('../' . $product['image']);
            }
        } else {
            $error = "Failed to upload new image!";
        }
    }

    if (empty($error)) {
        if ($productModel->updateProduct($productId, $productData)) {
            $message = "Product updated successfully!";
            // Refresh product data
            $product = $productModel->getProductById($productId);
        } else {
            $error = "Failed to update product!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Product - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Product</h1>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="laptops" <?php echo $product['category'] === 'laptops' ? 'selected' : ''; ?>>Laptops</option>
                                    <option value="smartphones" <?php echo $product['category'] === 'smartphones' ? 'selected' : ''; ?>>Smartphones</option>
                                    <option value="tablets" <?php echo $product['category'] === 'tablets' ? 'selected' : ''; ?>>Tablets</option>
                                    <option value="accessories" <?php echo $product['category'] === 'accessories' ? 'selected' : ''; ?>>Accessories</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                            </div>
                            <?php if (isset($product['image'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Current Image</label>
                                <div>
                                    <img src="<?php echo '../' . htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         style="max-width: 200px; height: auto;">
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="edit_product" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 