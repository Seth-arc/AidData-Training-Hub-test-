from pathlib import Path

PATCHES = {
    'assets/css/learnpress.min.css': [
        (
            '#popup-course{display:flex;position:fixed;z-index:99999;top:0;right:0;bottom:0;left:0;background:var(--lp-bg-color-lesson,#fff)}',
            '#popup-course{display:flex;position:fixed;z-index:99999;top:0;right:0;bottom:0;left:0;background:var(--lp-bg-color-lesson,#fff);--lp-popup-sidebar-offset:96px}',
        ),
        (
            '#popup-sidebar{overflow:auto;position:relative;flex:0 0 475px;padding-top:10px;margin-top:40px;-webkit-transition:.25s;-moz-transition:.25s;-ms-transition:.25s;-o-transition:.25s;transition:.25s}',
            '#popup-sidebar{position:sticky;top:var(--lp-popup-sidebar-offset,96px);height:calc(100vh - var(--lp-popup-sidebar-offset,96px));overflow:hidden;flex:0 0 475px;padding-top:0;margin-top:0;align-self:flex-start;-webkit-transition:.25s;-moz-transition:.25s;-ms-transition:.25s;-o-transition:.25s;transition:.25s}',
        ),
        (
            '#popup-sidebar .course-curriculum{overflow:auto;position:absolute;top:70px;bottom:0;width:475px}',
            '#popup-sidebar .course-curriculum{overflow:auto;position:relative;width:100%;height:100%;padding:16px 0 24px}',
        ),
        (
            '#popup-sidebar .curriculum-more{padding-right:16px;padding-left:16px}',
            '#popup-sidebar .curriculum-more{padding-right:16px;padding-left:16px}@media(max-width:1024px){#popup-sidebar{position:relative;top:auto;height:auto;padding-top:10px;margin-top:40px;overflow:auto}#popup-sidebar .course-curriculum{height:auto;padding:0}}',
        ),
    ],
    'assets/css/learnpress-rtl.min.css': [
        (
            '#popup-course{display:flex;position:fixed;z-index:99999;top:0;left:0;bottom:0;right:0;background:var(--lp-bg-color-lesson,#fff)}',
            '#popup-course{display:flex;position:fixed;z-index:99999;top:0;left:0;bottom:0;right:0;background:var(--lp-bg-color-lesson,#fff);--lp-popup-sidebar-offset:96px}',
        ),
        (
            '#popup-sidebar{overflow:auto;position:relative;flex:0 0 475px;padding-top:10px;margin-top:40px;-webkit-transition:.25s;-moz-transition:.25s;-ms-transition:.25s;-o-transition:.25s;transition:.25s}',
            '#popup-sidebar{position:sticky;top:var(--lp-popup-sidebar-offset,96px);height:calc(100vh - var(--lp-popup-sidebar-offset,96px));overflow:hidden;flex:0 0 475px;padding-top:0;margin-top:0;align-self:flex-start;-webkit-transition:.25s;-moz-transition:.25s;-ms-transition:.25s;-o-transition:.25s;transition:.25s}',
        ),
        (
            '#popup-sidebar .course-curriculum{overflow:auto;position:absolute;top:70px;bottom:0;width:475px}',
            '#popup-sidebar .course-curriculum{overflow:auto;position:relative;width:100%;height:100%;padding:16px 0 24px}',
        ),
        (
            '#popup-sidebar .curriculum-more{padding-left:16px;padding-right:16px}',
            '#popup-sidebar .curriculum-more{padding-left:16px;padding-right:16px}@media(max-width:1024px){#popup-sidebar{position:relative;top:auto;height:auto;padding-top:10px;margin-top:40px;overflow:auto}#popup-sidebar .course-curriculum{height:auto;padding:0}}',
        ),
    ],
}


def main() -> None:
    for relative_path, replacements in PATCHES.items():
        path = Path(relative_path)
        text = path.read_text()
        for old, new in replacements:
            if old not in text:
                raise SystemExit(f'pattern not found in {path}: {old}')
            text = text.replace(old, new, 1)
        path.write_text(text)


if __name__ == '__main__':
    main()
