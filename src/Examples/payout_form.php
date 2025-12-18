<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/3.0.0/jquery.payment.min.js" integrity="sha512-vBu5d4hztWcpvKmp+qUS8afvWUMjTd59Z7ci0j6YnKu83yy6Xh/VxtgZqIteIFaK3gMYDm0AnOp3pEF4z6afMw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
    .padding{

        padding: 5rem !important;
    }
    .form-control:focus {
        box-shadow: 10px 0px 0px 0px #ffffff !important;
        border-color: #4ca746;
    }
</style>
<script>
    $(function($) {
        $('[data-numeric]').payment('restrictNumeric');
        $('.cc-number').payment('formatCardNumber');
        $.fn.toggleInputError = function(erred) {
            this.parent('.form-group').toggleClass('has-error', erred);
            return this;
        };
        $('form').submit(function(e) {
            e.preventDefault();
            var cardType = $.payment.cardType($('.cc-number').val());
            $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
            $('.cc-brand').text(cardType);
            $('.validation').removeClass('text-danger text-success');
            $('.validation').addClass($('.has-error').length ? 'text-danger' : 'text-success');
        });
    });
</script>

<div class="container-fluid">
    <form method="post">
            <div class="row mt-5 mb-5">
                <div class="container-fluid d-flex justify-content-center">
                    <div class="col-sm-8 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <bold>Выплата на банковскую карту</bold>
                                    </div>
                                    <div class="col-md-6 text-right float-end" style="margin-top: -5px; text-align: right;">
                                        <img src="https://img.icons8.com/color/36/000000/mir.png">
                                        <img src="https://img.icons8.com/color/36/000000/visa.png">
                                        <img src="https://img.icons8.com/color/36/000000/mastercard.png">
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" style="min-height: 250px">
                                <div class="form-group mb-3">
                                    <label for="cc-number" class="control-label">Номер Карты</label>
                                    <input name="cc-number" id="cc-number" type="number" class="input-lg form-control cc-number" autocomplete="cc-number" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" required autofocus>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="summ" class="control-label">Сумма в рублях</label>
                                    <input name="summ" id="summ" type="number" min="1" step="0.01" class="input-lg form-control" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="reciever-name" class="control-label">Имя и Фамилия Получателя</label>
                                    <input name="reciever-name" id="reciever-name" type="text" class="input-lg form-control">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="description" class="control-label">Назначение платежа</label>
                                    <textarea name="description" id="description" class="input-lg form-control" maxlength="255"></textarea>
                                </div>

                                <div class="form-group">
                                    <input value="Выплатить" type="submit" class="btn btn-success btn-lg form-control" style="font-size: .8rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
