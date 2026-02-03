<div class="card">
    <div class="card-body paper" id="paperPreview">
        <table>
            <tr>
                <td class="label-cell" rowspan="2">Unit kerja</td>
                <td rowspan="2">
                    <?= nl2br(htmlspecialchars($data['unit_kerja'] ?? '')) ?>
                </td>
                <td width="10%">Tanggal</td>
                <td>
                    <?= htmlspecialchars($data['tanggal_fmt'] ?? '') ?>
                </td>
            </tr>
            <tr>
                <td>Pukul</td>
                <td>
                    <?= htmlspecialchars($data['pukul_mulai'] ?? '') ?>
                    –
                    <?= htmlspecialchars($data['pukul_selesai'] ?? '') ?> WIB
                </td>
            </tr>
            <tr>
                <td>Pimpinan Rapat</td>
                <td><?= nl2br(htmlspecialchars($data['pimpinan'] ?? '')) ?></td>
                <td>Tempat</td>
                <td><?= nl2br(htmlspecialchars($data['tempat'] ?? '')) ?></td>
            </tr>
            <tr>
                <td>Topik</td>
                <td colspan="3"><?= nl2br(htmlspecialchars($data['topik'] ?? '')) ?></td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td colspan="3"><?= nl2br(htmlspecialchars($data['lampiran'] ?? '')) ?></td>
            </tr>
        </table>

        <div class="spacer"></div>

        <table>
            <tr>
                <td class="label-cell">Peserta :</td>
            </tr>
            <tr>
                <td><?= nl2br(htmlspecialchars($data['peserta'] ?? '')) ?></td>
            </tr>

            <tr>
                <td class="label-cell">Agenda :</td>
            </tr>
            <tr>
                <td><?= nl2br(htmlspecialchars($data['agenda'] ?? '')) ?></td>
            </tr>

            <tr>
                <td><em>Resume:</em></td>
            </tr>
        </table>

        <div class="preview-box">
            <strong>Pembukaan</strong>
            <div><?= $data['pembukaan'] ?? '' ?></div>
            <br>
            <strong>Pembahasan dan Diskusi</strong>
            <div><?= $data['pembahasan'] ?? '' ?></div>
        </div>
    </div>
</div>
