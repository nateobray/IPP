<?php
namespace obray\ipp\enums;

class Finishings  extends \obray\ipp\types\Enum
{
    const none = 3;
    const staple = 4;
    const punch = 5;
    const cover = 6;
    const bind = 7;
    const saddle_stitch = 8;
    const edge_stitch = 9;

    const staple_top_left = 20;
    const staple_bottom_left = 21;
    const staple_top_right = 22;
    const staple_bottom_right = 23;
    const edge_stitch_left = 24;
    const edge_stitch_top = 25;
    const edge_stitch_right = 26;
    const edge_stitch_bottom = 27;
    const staple_dual_left = 28;
    const staple_dual_top = 29;
    const staple_dual_right = 30;
    const staple_dual_bottom = 31;

    // PWG5100.1 IPP Finishings 2.1 — general operations (10–16)
    const fold = 10;
    const trim = 11;
    const bale = 12;
    const booklet_maker = 13;
    const jog_offset = 14;
    const coat = 15;
    const laminate = 16;

    // PWG5100.1 — triple-staple positions (32–35)
    const staple_triple_left = 32;
    const staple_triple_top = 33;
    const staple_triple_right = 34;
    const staple_triple_bottom = 35;

    // PWG5100.1 — bind positions (50–53)
    const bind_left = 50;
    const bind_top = 51;
    const bind_right = 52;
    const bind_bottom = 53;

    // PWG5100.1 — trim-after positions (60–63)
    const trim_after_pages = 60;
    const trim_after_documents = 61;
    const trim_after_copies = 62;
    const trim_after_job = 63;

    // PWG5100.1 — punch positions (70–85)
    const punch_top_left = 70;
    const punch_bottom_left = 71;
    const punch_top_right = 72;
    const punch_bottom_right = 73;
    const punch_dual_left = 74;
    const punch_dual_top = 75;
    const punch_dual_right = 76;
    const punch_dual_bottom = 77;
    const punch_triple_left = 78;
    const punch_triple_top = 79;
    const punch_triple_right = 80;
    const punch_triple_bottom = 81;
    const punch_quad_left = 82;
    const punch_quad_top = 83;
    const punch_quad_right = 84;
    const punch_quad_bottom = 85;

    // PWG5100.1 — fold variants (90–101)
    const fold_accordion = 90;
    const fold_double_gate = 91;
    const fold_engineering_z = 92;
    const fold_gate = 93;
    const fold_half = 94;
    const fold_half_z = 95;
    const fold_left_gate = 96;
    const fold_letter = 97;
    const fold_parallel = 98;
    const fold_poster = 99;
    const fold_right_gate = 100;
    const fold_z = 101;
}