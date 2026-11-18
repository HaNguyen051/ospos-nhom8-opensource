<?php
/**
 * Module 4 - Cafe POS receipt (standard point-of-sale receipt, not tax invoice).
 * The Sales controller provides the same variables as receipt_default.php.
 * Monetary values use OSPOS to_currency() and the configured Vietnamese locale.
 * Do not duplicate payment/tax calculations here.
 */
$receiptFontSize   = max(10, min(20, (int) ($config['receipt_font_size'] ?? 12)));
$tableId           = isset($dinner_table) ? (int) $dinner_table : 0;
$tableLabel        = $tableId === 1 ? 'Giao hàng' : ($tableId === 2 ? 'Mang đi' : ($tableId > 2 ? 'Bàn số ' . $tableId : ''));
$showTaxes         = ! empty($config['receipt_show_taxes']);
$showTotalDiscount = ! empty($config['receipt_show_total_discount']);
$showDescriptions  = ! empty($config['receipt_show_description']);
$showSerials       = ! empty($config['receipt_show_serialnumber']);
$showTaxFlags      = ! empty($config['receipt_show_tax_ind']);
// Cafe POS is the default receipt brand. The administrator can change it in Configuration > Company.
$companyName = trim((string) ($config['company'] ?? ''));
if ($companyName === '') {
    $companyName = 'Cafe POS';
}
?>
<style>
/* Scoped to this receipt to avoid affecting the OSPOS admin UI. */
#receipt_wrapper.cafe-receipt { width: 80mm; max-width: 100%; margin: 12px auto; padding: 12px 9px; color: #201b17; background: white; font-family: Arial, 'DejaVu Sans', sans-serif; line-height: 1.4; box-sizing: border-box; }
#receipt_wrapper.cafe-receipt * { box-sizing: border-box; }
.cafe-receipt .cafe-center { text-align: center; }
.cafe-receipt .cafe-name { font-size: 1.35em; font-weight: 800; letter-spacing: .03em; overflow-wrap: anywhere; }
.cafe-receipt .cafe-subtitle { font-size: 1.04em; font-weight: 700; margin-top: 9px; }
.cafe-receipt .cafe-muted { color: #555; font-size: .92em; }
.cafe-receipt .cafe-rule { border: 0; border-top: 1px dashed #777; margin: 10px 0; }
.cafe-receipt .cafe-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin: 3px 0; }
.cafe-receipt .cafe-row span:last-child { text-align: right; }
.cafe-receipt .cafe-items { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 8px; }
.cafe-receipt .cafe-items th { border-bottom: 1px solid #aaa; padding: 5px 2px; text-align: left; }
.cafe-receipt .cafe-items td { vertical-align: top; padding: 6px 2px; overflow-wrap: anywhere; }
.cafe-receipt .cafe-items th:nth-child(1) { width: 46%; }
.cafe-receipt .cafe-items th:nth-child(2) { width: 23%; }
.cafe-receipt .cafe-items th:nth-child(3) { width: 12%; text-align: center; }
.cafe-receipt .cafe-items th:nth-child(4) { width: 19%; text-align: right; }
.cafe-receipt .cafe-items .cafe-number { text-align: right; white-space: nowrap; }
.cafe-receipt .cafe-items .cafe-quantity { text-align: center; }
.cafe-receipt .cafe-items .cafe-note { font-size: .88em; color: #555; padding-top: 0; }
.cafe-receipt .cafe-total { font-size: 1.14em; font-weight: 800; padding: 6px 0; border-top: 1px solid #555; }
.cafe-receipt .cafe-thanks { text-align: center; margin-top: 14px; font-style: italic; }
.cafe-receipt #barcode { text-align: center; margin-top: 10px; }
.cafe-receipt #barcode svg, .cafe-receipt #barcode img { max-width: 100%; height: auto; }
.cafe-receipt .cafe-logo img { max-height: 55px; max-width: 80%; object-fit: contain; }
@media print {
  @page { size: 80mm auto; margin: 3mm; }
  #receipt_wrapper.cafe-receipt { width: 74mm; max-width: 74mm; margin: 0; padding: 0; box-shadow: none; }
  #receipt_wrapper.cafe-receipt .cafe-muted { color: #222; }
}
</style>
<div id="receipt_wrapper" class="cafe-receipt" style="font-size: <?= $receiptFontSize ?>px;">
  <div id="receipt_header" class="cafe-center">
    <?php if (! empty($config['company_logo'])): ?>
      <div class="cafe-logo"><img src="<?= base_url('uploads/' . esc($config['company_logo'], 'url')) ?>" alt="Logo quán"></div>
    <?php endif; ?>
    <div class="cafe-name"><?= esc($companyName) ?></div>
    <?php if (! empty($config['address'])): ?><div><?= nl2br(esc($config['address'])) ?></div><?php endif; ?>
    <?php if (! empty($config['phone'])): ?><div>Điện thoại: <?= esc($config['phone']) ?></div><?php endif; ?>
    <div class="cafe-subtitle">PHIẾU THANH TOÁN</div>
    <div class="cafe-muted"><?= esc((string) ($transaction_time ?? '')) ?></div>
  </div>
  <hr class="cafe-rule">
  <div id="receipt_general_info">
    <div class="cafe-row"><span>Mã đơn:</span><strong><?= esc((string) ($sale_id ?? '')) ?></strong></div>
    <?php if (! empty($invoice_number)): ?>
      <div class="cafe-row"><span>Số hóa đơn:</span><span><?= esc((string) $invoice_number) ?></span></div>
    <?php endif; ?>
    <?php if ($tableLabel !== ''): ?>
      <div class="cafe-row"><span>Hình thức / bàn:</span><strong><?= esc($tableLabel) ?></strong></div>
    <?php endif; ?>
    <?php if (! empty($employee)): ?><div class="cafe-row"><span>Nhân viên:</span><span><?= esc($employee) ?></span></div><?php endif; ?>
    <?php if (! empty($customer)): ?><div class="cafe-row"><span>Khách hàng:</span><span><?= esc($customer) ?></span></div><?php endif; ?>
  </div>
  <hr class="cafe-rule">
  <table id="receipt_items" class="cafe-items">
    <thead><tr><th>Món / sản phẩm</th><th>Đơn giá</th><th>SL</th><th>Thành tiền</th></tr></thead>
    <tbody>
    <?php foreach (($cart ?? []) as $item): ?>
      <?php if (($item['print_option'] ?? null) !== PRINT_YES) {
          continue;
      } ?>
      <?php
        $displayName  = trim((string) ($item['name'] ?? '') . ' ' . (string) ($item['attribute_values'] ?? ''));
        $lineTotalKey = $showTotalDiscount ? 'total' : 'discounted_total';
        $lineTotal    = $item[$lineTotalKey] ?? $item['discounted_total'] ?? 0;
        $description  = trim((string) ($item['description'] ?? ''));
        $serialNumber = trim((string) ($item['serialnumber'] ?? ''));
        ?>
      <tr>
        <td><strong><?= esc($displayName) ?></strong></td>
        <td class="cafe-number"><?= to_currency($item['price'] ?? 0) ?></td>
        <td class="cafe-quantity"><?= to_quantity_decimals($item['quantity'] ?? 0) ?></td>
        <td class="cafe-number"><?= to_currency($lineTotal) ?></td>
      </tr>
      <?php if (($showDescriptions && $description !== '') || ($showSerials && $serialNumber !== '') || ($showTaxFlags && ! empty($item['taxed_flag']))): ?>
      <tr><td class="cafe-note" colspan="4">
        <?php if ($showDescriptions && $description !== ''): ?>Ghi chú/mô tả: <?= esc($description) ?><?php endif; ?>
        <?php if ($showSerials && $serialNumber !== ''): ?><?= $description !== '' ? ' · ' : '' ?>Số sê-ri: <?= esc($serialNumber) ?><?php endif; ?>
        <?php if ($showTaxFlags && ! empty($item['taxed_flag'])): ?><?= esc((string) $item['taxed_flag']) ?><?php endif; ?>
      </td></tr>
      <?php endif; ?>
      <?php if (! empty($item['discount']) && $item['discount'] > 0): ?>
        <tr><td colspan="3" class="cafe-note">Giảm giá món <?= ($item['discount_type'] ?? null) === FIXED ? to_currency($item['discount']) : esc((string) $item['discount']) . '%' ?></td>
          <td class="cafe-number"><?= to_currency($item['discounted_total'] ?? 0) ?></td></tr>
      <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
  </table>
  <hr class="cafe-rule">
  <?php if ($showTotalDiscount && ! empty($discount)): ?>
    <div class="cafe-row"><span>Tạm tính trước giảm giá:</span><span><?= to_currency($prediscount_subtotal ?? 0) ?></span></div>
    <div class="cafe-row"><span>Giảm giá khách hàng:</span><span><?= to_currency(-1 * (float) $discount) ?></span></div>
  <?php endif; ?>
  <?php if ($showTaxes): ?>
    <div class="cafe-row"><span>Tạm tính:</span><span><?= to_currency($subtotal ?? 0) ?></span></div>
    <?php foreach (($taxes ?? []) as $tax): ?>
      <div class="cafe-row"><span>Thuế <?= esc((string) ($tax['tax_group'] ?? '')) ?> (<?= esc((string) ($tax['tax_rate'] ?? '')) ?>%):</span><span><?= to_currency_tax($tax['sale_tax_amount'] ?? 0) ?></span></div>
    <?php endforeach; ?>
  <?php endif; ?>
  <div class="cafe-row cafe-total"><span>TỔNG CỘNG</span><span><?= to_currency($total ?? 0) ?></span></div>
  <?php foreach (($payments ?? []) as $payment): ?>
    <?php $paymentParts = explode(':', (string) ($payment['payment_type'] ?? '')); ?>
    <div class="cafe-row"><span><?= esc($paymentParts[0]) ?>:</span><span><?= to_currency(-1 * (float) ($payment['payment_amount'] ?? 0)) ?></span></div>
  <?php endforeach; ?>
  <?php if (isset($amount_change)): ?>
    <div class="cafe-row"><span><?= (float) $amount_change >= 0 ? 'Tiền trả lại:' : 'Còn phải trả:' ?></span><strong><?= to_currency($amount_change) ?></strong></div>
  <?php endif; ?>
  <?php if (! empty($comments)): ?><hr class="cafe-rule"><div>Ghi chú đơn hàng: <?= nl2br(esc($comments)) ?></div><?php endif; ?>
  <?php if (! empty($config['return_policy'])): ?><div class="cafe-muted" id="sale_return_policy"><?= nl2br(esc($config['return_policy'])) ?></div><?php endif; ?>
  <div class="cafe-thanks">Cảm ơn quý khách và hẹn gặp lại!</div>
  <?php if (! empty($barcode)): ?><div id="barcode"><?= $barcode ?><br><?= esc((string) ($sale_id ?? '')) ?></div><?php endif; ?>
</div>
