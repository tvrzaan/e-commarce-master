<?php
session_start();
require_once('../autoload.php');
require_once('../includes/functions.php');
require_once('../models/Product.php');

use App\Models\Product;

// Check if user is admin, if not redirect to home page
if (!is_admin()) {
    header('Location: ../index.php');
    exit();
}

$productModel = new Product();

// Get all categories for the dropdown
$categories = $productModel->getAllCategories();

// Handle product deletion
if (isset($_GET['delete_product']) && is_numeric($_GET['delete_product'])) {
    if ($productModel->deleteProduct($_GET['delete_product'])) {
        $message = "Product deleted successfully!";
    } else {
        $error = "Failed to delete product!";
    }
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $uploadDir = '../uploads/products/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempName = $_FILES['image']['tmp_name'];
        $originalName = $_FILES['image']['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($tempName, $targetPath)) {
            $productData = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'category' => $_POST['category'],
                'image' => 'uploads/products/' . $fileName,
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'status' => 'active'
            ];

            if ($productModel->addProduct($productData)) {
                $message = "Product added successfully!";
            } else {
                $error = "Failed to add product to database!";
                // Clean up uploaded file if database insert failed
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
            }
        } else {
            $error = "Failed to upload image!";
        }
    } else {
        $error = "Please select an image!";
    }
}

// Get all products for display
$products = $productModel->getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container my-4">
        <h1 class="mb-4">Admin Dashboard</h1>

        <?php if (isset($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Add New Product Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add New Product</h5>
            </div>
            <div class="card-body">
                <form method="post" action="dashboard.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <option value="other">Add New Category</option>
                                </select>
                                <div id="newCategoryDiv" style="display: none;" class="mt-2">
                                    <input type="text" class="form-control" id="newCategory" name="new_category" 
                                           placeholder="Enter new category name">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                       value="0" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>

        <!-- Products List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Manage Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="<?php echo '../' . htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         style="width: 50px; height: 50px; object-fit: contain;">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo isset($product['stock_quantity']) ? $product['stock_quantity'] : '0'; ?></td>
                                <td><?php echo isset($product['status']) ? ucfirst($product['status']) : 'Active'; ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="dashboard.php?delete_product=<?php echo $product['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Handle new category input
    document.getElementById('category').addEventListener('change', function() {
        const newCategoryDiv = document.getElementById('newCategoryDiv');
        const newCategoryInput = document.getElementById('newCategory');
        
        if (this.value === 'other') {
            newCategoryDiv.style.display = 'block';
            newCategoryInput.required = true;
        } else {
            newCategoryDiv.style.display = 'none';
            newCategoryInput.required = false;
        }
    });
    </script>
</body>
</html> 