<!doctype html>
<html>
<head>
  <title>Chapter Marker Player Example</title>
  <style type="text/css">
        /* BEGIN_INCLUDE(override_default) */
        ol.chapter-list {
          list-style-type: none;
          padding: 0;
          margin: 0;
        }
        
        ol.chapter-list > li {
          padding: 0.5em;
          margin: 0;
          cursor: pointer;
        }
        /* END_INCLUDE(override_default) */
        
        /* BEGIN_INCLUDE(alternate_background) */
        ol.chapter-list > li:nth-child(odd) {
          background-color: #ececec;
        }
        
        ol.chapter-list > li:nth-child(even) {
          background-color: #e4e4e4;
        }
        /* END_INCLUDE(alternate_background) */
        
        /* BEGIN_INCLUDE(hover_background) */
        ol.chapter-list > li:hover {
          background-color: #c0c0c0;
        }
        /* END_INCLUDE(hover_background) */
  </style>
  <script type="text/javascript" src="/ADNBP/js/ChapterMarkerPlayer.js"></script>
</head>
<body>
  <h1>Chapter Marker Player Example</h1>
  <h3>Google I/O 2011: YouTube's iframe Player: The Future of Embedding</h3>
  <div id="iframe-session-player"></div>

  <h3>Google I/O 2011: The YouTube Caption API, Speech Recognition, and WebVTT captions for HTML5</h3>
  <div id="captions-session-player"></div>

  <script type="text/javascript">
    ChapterMarkerPlayer.insert({
      container: 'iframe-session-player',
      videoId: 'bHQqvYy5KYo',
      width: 600,
// BEGIN_INCLUDE(define_chapters)
      chapters: {
        0: 'Start',
        5: 'Introductions',
        26: 'Agenda',
        252: '<iframe> Tech Details',
        1040: 'Comparing the Two APIs',
        1670: 'Example Application',
        2662: 'Questions & Answers'
      }
// END_INCLUDE(define_chapters)
    });

    ChapterMarkerPlayer.insert({
      container: 'captions-session-player',
      videoId: 'tua3DdacgOo',
      width: 600,
      chapters: {
        0: 'Start',
        6: 'Introductions',
        50: 'Overview',
        240: 'Real-Time Captions for I/O Live',
        320: 'Caption Gadget for Live Events',
        646: 'Timed Text for HTML5',
        1624: 'YouTube Captions API',
        2813: 'Questions & Answers'
      }
    });
  </script>
</body>
</html>