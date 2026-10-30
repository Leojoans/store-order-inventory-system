<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Billing | Inventory System</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f1f5f9;
            color: #1e293b;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1150px;
            margin: 35px auto;
            padding: 0 18px;
        }

        .header {
            background: #1e293b;
            color: white;
            padding: 22px 26px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .header h1 { margin: 0; font-size: 23px; }
        .header p { margin: 6px 0 0; color: #cbd5e1; font-size: 13px; }

        .content {
            background: white;
            padding: 26px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 20px #0f172a0c;
        }

        .section { margin-bottom: 28px; }
        .section h2 { font-size: 17px; margin: 0 0 15px; }

        .customer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: white;
            color: #1e293b;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }

        .billing-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(240px, 1fr);
            gap: 22px;
            align-items: start;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 12px 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        th { background: #e2e8f0; }

        .quantity { min-width: 72px; }
        .product-select { min-width: 180px; }

        .button {
            display: inline-block;
            border: 0;
            border-radius: 6px;
            padding: 11px 16px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }

        .button:disabled { opacity: .55; cursor: not-allowed; }
        .primary { background: #2563eb; color: white; }
        .success { background: #15803d; color: white; }
        .secondary { background: #e2e8f0; color: #1e293b; }
        .danger { background: #fee2e2; color: #b91c1c; padding: 8px 10px; }

        .add-row { margin-top: 12px; }

        .alert {
            border: 1px solid #f59e0b;
            background: #fffbeb;
            color: #92400e;
            border-radius: 8px;
            padding: 18px;
        }

        .alert h3 { margin: 0 0 12px; font-size: 15px; }
        .alert ul { margin: 0; padding-left: 20px; }
        .alert li { margin: 8px 0; font-size: 13px; }

        .empty-stock { font-size: 13px; color: #64748b; }

        .summary {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 18px;
            background: #f8fafc;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            font-size: 14px;
        }

        .grand-total {
            border-top: 1px solid #cbd5e1;
            margin-top: 8px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .payment-input { margin-top: 18px; }

        .message {
            display: none;
            padding: 13px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            white-space: pre-line;
        }

        .message.error {
            display: block;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .message.ok {
            display: block;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .footer-actions { margin-top: 18px; }
        .muted { color: #64748b; font-size: 12px; margin-top: 10px; }

        @media (max-width: 760px) {
            .container { margin: 15px auto; padding: 0 10px; }
            .content { padding: 16px; }
            .header { padding: 18px; }
            .header h1 { font-size: 19px; }
            .customer-grid, .billing-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div>
            <h1>Store Billing</h1>
            <p>New order · Inventory management</p>
        </div>
        <span>Laravel Store</span>
    </header>

    <main class="content">
        <div id="message" class="message" role="status" aria-live="polite"></div>

        <form id="orderForm">
            <section class="section">
                <h2>Customer details</h2>

                <div class="customer-grid">
                    <div>
                        <label for="customerEmail">Customer email</label>
                        <input
                            id="customerEmail"
                            type="email"
                            maxlength="255"
                            placeholder="arun@example.com"
                            required
                        >
                    </div>

                    <div>
                        <label for="customerName">Customer name</label>
                        <input
                            id="customerName"
                            type="text"
                            maxlength="255"
                            placeholder="Arun Kumar"
                            required
                        >
                    </div>
                </div>
            </section>

            <div class="billing-grid">
                <section class="section">
                    <h2>Products</h2>

                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Tax</th>
                                <th>Line total</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody id="productRows"></tbody>
                        </table>
                    </div>

                    <button type="button" class="button primary add-row" id="addProduct">
                        + Add product
                    </button>
                </section>

                <aside>
                    <section class="section alert">
                        <h3>Low-stock alert</h3>

                        @if($lowStockProducts->isEmpty())
                            <p class="empty-stock">No low-stock products.</p>
                        @else
                            <p class="muted">
                                Products with stock below {{ $threshold }} units
                            </p>
                            <ul>
                                @foreach($lowStockProducts as $product)
                                    <li>
                                        {{ $product->name }} —
                                        {{ $product->stock_on_hand }} units left
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>

                    <section class="section summary">
                        <h2>Payment summary</h2>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong id="subtotal">₹0.00</strong>
                        </div>

                        <div class="summary-row">
                            <span>Tax</span>
                            <strong id="tax">₹0.00</strong>
                        </div>

                        <div class="summary-row grand-total">
                            <span>Grand total</span>
                            <span id="grandTotal">₹0.00</span>
                        </div>

                        <div class="payment-input">
                            <label for="amountPaid">Amount given by customer (₹)</label>
                            <input
                                id="amountPaid"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="250.00"
                            >
                        </div>

                        <div class="summary-row">
                            <span>Balance to return</span>
                            <strong id="changeDue">₹0.00</strong>
                        </div>

                        <p class="muted">
                            Change is displayed only when the payment covers the total.
                            The order API remains responsible for calculating the bill.
                        </p>

                        <div class="footer-actions">
                            <button type="submit" class="button success" id="generateBill">
                                Generate bill
                            </button>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </main>
</div>

<script>
    const products = @json($products);
    const rows = document.getElementById('productRows');
    const form = document.getElementById('orderForm');
    const message = document.getElementById('message');
    const submitButton = document.getElementById('generateBill');

    const money = cents =>
        '₹' + (cents / 100).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    function showMessage(text, type) {
        message.textContent = text;
        message.className = 'message ' + type;
        message.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function addProductRow() {
        const row = document.createElement('tr');

        const productCell = document.createElement('td');
        const select = document.createElement('select');
        select.className = 'product-select';
        select.required = true;
        select.setAttribute('aria-label', 'Product');

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select product';
        select.appendChild(placeholder);

        products.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent =
                `${product.name} (${product.stock_on_hand} available)`;
            select.appendChild(option);
        });

        productCell.appendChild(select);

        const quantityCell = document.createElement('td');
        const quantity = document.createElement('input');
        quantity.type = 'number';
        quantity.min = '1';
        quantity.step = '1';
        quantity.value = '1';
        quantity.required = true;
        quantity.className = 'quantity';
        quantity.setAttribute('aria-label', 'Quantity');
        quantityCell.appendChild(quantity);

        const priceCell = document.createElement('td');
        const taxCell = document.createElement('td');
        const totalCell = document.createElement('td');
        const actionCell = document.createElement('td');

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'button danger';
        removeButton.textContent = 'Remove';
        removeButton.addEventListener('click', () => {
            row.remove();
            calculateTotals();
        });

        actionCell.appendChild(removeButton);
        row.append(
            productCell,
            quantityCell,
            priceCell,
            taxCell,
            totalCell,
            actionCell
        );

        select.addEventListener('change', calculateTotals);
        quantity.addEventListener('input', calculateTotals);

        row._fields = { select, quantity, priceCell, taxCell, totalCell };
        rows.appendChild(row);

        calculateTotals();
    }

    function calculateTotals() {
        let subtotalCents = 0;
        let taxCents = 0;

        Array.from(rows.children).forEach(row => {
            const fields = row._fields;
            const product = products.find(
                p => String(p.id) === fields.select.value
            );

            const quantity = Number(fields.quantity.value);

            if (!product || !Number.isSafeInteger(quantity) || quantity < 1) {
                fields.priceCell.textContent = '—';
                fields.taxCell.textContent = '—';
                fields.totalCell.textContent = '—';
                return;
            }

            const priceCents = Math.round(Number(product.price) * 100);
            const lineSubtotal = priceCents * quantity;
            const taxBasisPoints = Math.round(
                Number(product.tax_percentage) * 100
            );
            const lineTax = Math.floor(
                (lineSubtotal * taxBasisPoints + 5000) / 10000
            );

            subtotalCents += lineSubtotal;
            taxCents += lineTax;

            fields.priceCell.textContent = money(priceCents);
            fields.taxCell.textContent = money(lineTax);
            fields.totalCell.textContent = money(lineSubtotal + lineTax);
        });

        document.getElementById('subtotal').textContent = money(subtotalCents);
        document.getElementById('tax').textContent = money(taxCents);
        document.getElementById('grandTotal').textContent =
            money(subtotalCents + taxCents);

        const paidValue = document.getElementById('amountPaid').value;
        const paidCents = paidValue === ''
            ? null
            : Math.round(Number(paidValue) * 100);

        document.getElementById('changeDue').textContent =
            paidCents !== null && paidCents >= subtotalCents + taxCents
                ? money(paidCents - subtotalCents - taxCents)
                : '₹0.00';
    }

    document.getElementById('addProduct')
        .addEventListener('click', addProductRow);

    document.getElementById('amountPaid')
        .addEventListener('input', calculateTotals);

    form.addEventListener('submit', async event => {
        event.preventDefault();
        message.className = 'message';

        const items = [];
        const seen = new Set();
        let invalid = false;

        Array.from(rows.children).forEach(row => {
            const { select, quantity } = row._fields;
            const productId = Number(select.value);
            const qty = Number(quantity.value);
            const product = products.find(p => p.id === productId);

            if (!product || !Number.isSafeInteger(qty) || qty < 1) {
                invalid = true;
                return;
            }

            if (seen.has(productId)) {
                invalid = true;
                showMessage(
                    'Please select each product only once. Update its quantity instead.',
                    'error'
                );
                return;
            }

            seen.add(productId);

            items.push({
                product_id: productId,
                quantity: qty
            });
        });

        if (invalid || items.length === 0) {
            if (message.className !== 'message error') {
                showMessage(
                    'Add at least one valid product and quantity.',
                    'error'
                );
            }
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Creating order...';

        try {
            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    customer: {
                        name: document.getElementById('customerName').value.trim(),
                        email: document.getElementById('customerEmail').value.trim()
                    },
                    items
                })
            });

            const result = await response.json();

            if (!response.ok) {
                const errors = result.errors
                    ? Object.values(result.errors).flat().join('\n')
                    : result.message || 'Unable to create the order.';

                showMessage(errors, 'error');
                return;
            }

            const order = result.data;

            showMessage(
                `Order #${order.id} created successfully.\n` +
                `Subtotal: ${money(Math.round(Number(order.subtotal) * 100))}\n` +
                `Tax: ${money(Math.round(Number(order.tax_amount) * 100))}\n` +
                `Grand total: ${money(Math.round(Number(order.grand_total) * 100))}`,
                'ok'
            );

            // Refresh stock and low-stock alerts from the server.
            window.setTimeout(() => window.location.reload(), 2500);

        } catch (error) {
            showMessage(
                'Could not connect to the server. Check that Laravel is running.',
                'error'
            );
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Generate bill';
        }
    });

    addProductRow();
</script>
</body>
</html>
