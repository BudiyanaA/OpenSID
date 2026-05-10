@php
  $today = date('Y-m-d');
  $customTime = "00:00:00";
  $githubFile = 'https://raw.githubusercontent.com/ariandii/json/main/calendar.json';

  $countdownEnabled = theme_config('countdown', false);
  $themeDate = theme_config('event_tgl');
  $themeDescription = theme_config('event_description');
  $themeSummary = theme_config('event_summary');
  $todayEvent = null;
  $result = null;

  function getUpcomingFromGithub($file, $customTime, $afterDate = null) {
      $json = @file_get_contents($file);
      if (!$json) return null;
      $data = json_decode($json, true);
      if (!is_array($data)) return null;

      $afterTs = strtotime(($afterDate ?? date('Y-m-d')) . ' 00:00:00');
      $nextEvent = null;
      $nextIndex = null;

      foreach ($data as $date => $event) {
          $eventTs = strtotime($date . ' ' . $customTime);
          if ($eventTs > $afterTs) {
              $nextEvent = $event;
              $nextIndex = $eventTs;
              break;
          }
      }

      return $nextEvent ? ['nextEvent' => $nextEvent, 'index' => $nextIndex] : null;
  }

  if ($countdownEnabled && $themeDate) {
      $themeDateYmd = date('Y-m-d', strtotime($themeDate));

      if ($themeDateYmd === $today) {
          // Hari ini adalah hari perayaan dari theme_config
          $todayEvent = [
              'date' => $themeDateYmd,
              'description' => [$themeDescription],
              'summary' => [$themeSummary],
          ];
          // Ambil countdown berikutnya dari GitHub
          $result = getUpcomingFromGithub($githubFile, $customTime, $today);
      } elseif ($themeDateYmd > $today) {
          // Tanggal event dari theme_config masih di masa depan → tampilkan countdown theme_config
          $result = [
              'nextEvent' => [
                  'date' => $themeDateYmd,
                  'description' => [$themeDescription],
                  'summary' => [$themeSummary],
              ],
              'index' => strtotime($themeDateYmd . ' ' . $customTime),
          ];
      }
  }

  if (!$todayEvent) {
      $githubJson = @file_get_contents($githubFile);
      $githubData = $githubJson ? json_decode($githubJson, true) : [];
      foreach ($githubData as $date => $event) {
          if (date('Y-m-d', strtotime($date)) === $today) {
              $todayEvent = [
                  'date' => $date,
                  'description' => $event['description'] ?? [],
                  'summary' => $event['summary'] ?? [],
              ];
              // Tetap ambil countdown berikutnya dari GitHub
              $result = getUpcomingFromGithub($githubFile, $customTime, $today);
          }
      }
  }

  if (!$result) {
      $result = getUpcomingFromGithub($githubFile, $customTime, $today);
  }
@endphp

@if ($todayEvent)
  <div class="text-center text-white mb-2" style="font-weight:bold; text-shadow:-1px -1px 0 rgba(0,0,0,.3),1px -1px 0 rgba(0,0,0,.3),-1px 1px 0 rgba(0,0,0,.3),1px 1px 0 rgba(0,0,0,.3)">
    Selamat Memperingati<br>🎉 {{ ucwords(implode(", ", (array)$todayEvent['summary'])) }} 🎉
  </div>
@endif

@if ($result && $result['nextEvent'])
<div id="countdown-{{ $result['index'] }}" class="text-center mr-3 ml-3 text-white">
  {{ ucwords(implode(", ", (array)$result['nextEvent']['description'])) }}
  <h5>{{ ucwords(implode(", ", (array)$result['nextEvent']['summary'])) }}</h5>
  <ul class="time-day">
      <li class="text-right">
          <p class="header-color-primary text-white">
              <span id="hari-{{ $result['index'] }}"></span>
              <small><span id="bln-thn-{{ $result['index'] }}"></span></small>
          </p>
          <h2><span id="tgl-{{ $result['index'] }}"></span></h2>
      </li>
      <li class="text-left">
          <h2><span id="days-{{ $result['index'] }}"></span></h2>
          <p class="header-color-primary text-white">
              <span id="hari-lagi-{{ $result['index'] }}"></span>
              <small><span id="time-{{ $result['index'] }}"></span></small>
          </p>
      </li>
  </ul>
</div>

<script>
(function () {
  const second = 1000, minute = second * 60, hour = minute * 60, day = hour * 24;
  const targetDate = new Date("{{ date('d M Y', $result['index']) }} {{ date('H:i:s', $result['index']) }}").getTime();
  const x = setInterval(function () {
      const now = new Date().getTime();
      const distance = targetDate - now;
      const days = Math.floor(distance / day);
      const hours = Math.floor((distance % day) / hour);
      const minutes = Math.floor((distance % hour) / minute);
      const seconds = Math.floor((distance % minute) / second);
      document.getElementById("days-{{ $result['index'] }}").innerText = days;
      document.getElementById("time-{{ $result['index'] }}").innerText = ("0"+hours).slice(-2)+":"+("0"+minutes).slice(-2)+":"+("0"+seconds).slice(-2);
      const eventDate = new Date("{{ date('Y-m-d', $result['index']) }}");
      const daysOfWeek = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
      document.getElementById("hari-{{ $result['index'] }}").innerText = daysOfWeek[eventDate.getDay()];
      document.getElementById("tgl-{{ $result['index'] }}").innerText = ("0" + eventDate.getDate()).slice(-2);
      document.getElementById("bln-thn-{{ $result['index'] }}").innerText = eventDate.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
      document.getElementById("hari-lagi-{{ $result['index'] }}").innerText = days <= 0 ? "Hari Ini" : "Hari Lagi";
      if (distance <= 0) clearInterval(x);
  }, 1000);
})();
</script>
@endif
