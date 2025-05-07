<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Add Product</h2>
    
    <form id="product-form" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Product Name" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
        </div>
        <div class="col-md-3">
            <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Add</button>
        </div>
    </form>

    <hr class="my-4">

    <h4>Submitted Products</h4>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Datetime Submitted</th>
                <th>Total Value</th>
                <th>Actions</th>

            </tr>
        </thead>
        <tbody id="product-table">
        @foreach($products as $p)
            <tr>
                <td>{{ $p['name'] }}</td>
                <td>{{ $p['quantity'] }}</td>
                <td>{{ number_format($p['price'], 2) }}</td>
                <td>{{ $p['datetime'] }}</td>
                <td>{{ number_format($p['total'], 2) }}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="openEditModal('{{ $p['id'] }}')">Edit</button>
                </td>
            </tr>
        @endforeach

            <tr class="table-secondary fw-bold">
                <td colspan="4" class="text-end">Total Sum</td>
                <td>{{ number_format($totalSum, 2) }}</td>
            </tr>
        </tbody>
    </table>
    <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form id="edit-form" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <input type="hidden" id="edit-id">
                  <div class="mb-3">
                      <label class="form-label">Product Name</label>
                      <input type="text" id="edit-name" class="form-control" required>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Quantity</label>
                      <input type="number" id="edit-quantity" class="form-control" required>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Price</label>
                      <input type="number" id="edit-price" class="form-control" step="0.01" required>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save changes</button>
              </div>
            </form>
          </div>
        </div>

</div>

<script>
    document.getElementById('product-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        const response = await fetch('/products', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            body: data
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to submit. Please check your inputs.');
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const products = @json($products);
    const modal = new bootstrap.Modal(document.getElementById('editModal'));

    function openEditModal(id) {
            const p = products.find(item => item.id === id);
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = p.name;
            document.getElementById('edit-quantity').value = p.quantity;
            document.getElementById('edit-price').value = p.price;
            modal.show();
        }


    document.getElementById('edit-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const id = document.getElementById('edit-id').value;
        const name = document.getElementById('edit-name').value;
        const quantity = document.getElementById('edit-quantity').value;
        const price = document.getElementById('edit-price').value;

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        const response = await fetch('/products/edit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ id, name, quantity, price })
        });

        if (response.ok) {
            modal.hide();
            location.reload();
        } else {
            alert('Failed to save changes.');
        }
    });
</script>


</body>
</html>
