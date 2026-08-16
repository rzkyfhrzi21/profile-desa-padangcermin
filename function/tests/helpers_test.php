<?php
declare(strict_types=1);

final class HelpersTest extends TestCase
{
    public function testSlugifyMengubahJudulMenjadiSlugBersih(): void
    {
        $this->assertSame('hello-world-test', slugify('Hello World Test!'));
        $this->assertSame('curug-embun-2', slugify('Curug Embun 2'));
        $this->assertSame('pekon-padang-cermin', slugify('  Pekon   Padang Cermin  '));
        $this->assertSame('', slugify('   '));
        $this->assertSame('abc-c', slugify('A/B_C - C!'));
    }

    public function testInisialNama(): void
    {
        $this->assertSame('BS', inisialNama('Budi Santoso'));
        $this->assertSame('A', inisialNama('Agus'));
        $this->assertSame('?', inisialNama(''));
        $this->assertSame('JP', inisialNama('  Joko   Prasojo  '));
    }

    public function testFormatTanggal(): void
    {
        $this->assertSame('-', formatTanggal(null));
        $this->assertSame('-', formatTanggal(''));
        $this->assertSame('16 Agustus 2026', formatTanggal('2026-08-16'));
        $this->assertSame('1 Januari 2025', formatTanggal('2025-01-01 10:00:00'));
    }

    public function testFormatAngka(): void
    {
        $this->assertSame('0', formatAngka(0));
        $this->assertSame('1.500', formatAngka(1500));
        $this->assertSame('1.000.000', formatAngka(1000000));
        $this->assertSame('5.064', formatAngka('5064'));
    }

    public function testTruncateMemotongDanMembuangTag(): void
    {
        $this->assertSame('Hello', truncate('Hello', 10));
        $this->assertSame('Halo…', truncate('<p><b>Halo</b></p>  dunia', 6));
        $this->assertSame('', truncate('', 10));
        $text = str_repeat('a', 200);
        $this->assertSame(155, mb_strlen(truncate($text, 155)));
        $this->assertSame('…', truncate($text, 1));
    }

    public function testEscapeHtml(): void
    {
        $this->assertSame('&lt;script&gt;', e('<script>'));
        $this->assertSame('a&#039;b', e("a'b"));
    }

    public function testUploadUrlDanFotoAda(): void
    {
        $this->assertSame('', uploadUrl(''));
        $this->assertStringContainsString('/uploads/x.jpg', uploadUrl('x.jpg'));
        $this->assertFalse(fotoAda('tidak-ada.jpg'));
        $this->assertFalse(fotoAda(''));
    }

    public function testIsPost(): void
    {
        $this->assertFalse(isPost());
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue(isPost());
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }
}
