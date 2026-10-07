<?php // Start PHP partial file ?>

<?php if (!isset($orders) || !$orders): ?> <!-- If $orders variable is missing or invalid -->
    <div class="msg error">Internal error: orders data not loaded.</div> <!-- Show error message -->
<?php elseif ($orders->num_rows === 0): ?> <!-- If there are no rows -->
    <p class="small">No orders found.</p> <!-- Show "no orders" message -->
<?php else: ?> <!-- If orders exist -->

<?php $queue_no = 1; ?> <!-- Create queue number counter (resets per status section) -->

<div class="table-wrap"> <!-- Wrapper for horizontal scrolling -->
    <table> <!-- Start table -->

        <tr> <!-- Header row -->
            <th>Queue</th> <!-- Queue number -->
            <th>Order ID</th> <!-- Database order id -->
            <th>Plate</th> <!-- Plate -->
            <th>Time and Date</th> <!-- Created time -->
            <th>Status</th> <!-- Status -->
            <th>Total (RM)</th> <!-- Total -->
            <th>Action</th> <!-- Action column -->
        </tr> <!-- End header row -->

        <?php while ($o = $orders->fetch_assoc()): ?> <!-- Loop each order row -->
            <tr> <!-- Start row -->

                <td><?php echo (int)$queue_no; ?></td> <!-- Print queue number -->
                <td><?php echo (int)$o["id"]; ?></td> <!-- Print order id -->
                <td><?php echo htmlspecialchars($o["plate_no"]); ?></td> <!-- Print plate safely -->
                <td><?php echo htmlspecialchars($o["created_at"]); ?></td> <!-- Print created time safely -->
                <td><?php echo htmlspecialchars($o["status"]); ?></td> <!-- Print status safely -->
                <td><?php echo number_format((float)$o["total_price"], 2); ?></td> <!-- Print total formatted -->

                <td>

<?php if ($o["status"] === "PAID"): ?>

<form method="POST" action="update_status.php" style="display:inline;">
<input type="hidden" name="order_id" value="<?php echo (int)$o["id"]; ?>">
<input type="hidden" name="new_status" value="WASHING">
<button type="submit" class="btn-action btn-wash">Start Wash</button>
</form>

<?php elseif ($o["status"] === "WASHING"): ?>

<form method="POST" action="update_status.php" style="display:inline;">
<input type="hidden" name="order_id" value="<?php echo (int)$o["id"]; ?>">
<input type="hidden" name="new_status" value="DONE">
<button type="submit" class="btn-action btn-done">Finish</button>
</form>

<?php endif; ?>

<div style="margin-top:6px;">
<a href="receipt.php?id=<?php echo (int)$o["id"]; ?>">Receipt</a> |
<a href="receipt_pdf.php?id=<?php echo (int)$o["id"]; ?>">PDF</a>
</div>

</td>

            </tr> <!-- End row -->

            <?php $queue_no++; ?> <!-- Increase queue number -->

        <?php endwhile; ?> <!-- End loop -->

    </table> <!-- End table -->
</div> <!-- End table wrapper -->

<?php endif; ?> <!-- End orders check ?>