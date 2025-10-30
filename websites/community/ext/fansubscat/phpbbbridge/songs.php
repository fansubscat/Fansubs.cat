<!doctype html>
<html lang="ca">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>Eina d’ordenació de cançons de Fansubs.cat</title>
	<style>
		*{
			box-sizing: border-box;
		}
		html,body{
			height: 100%;
			margin: 0;
			font-family: Inter,system-ui,Segoe UI,Roboto,Arial;
			background: rgb(250, 250, 250);
			color: rgb(0, 0, 0);
		}
		body{
			user-select: none;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
		}
		.app{
			display: flex;
			gap: 16px;
			height: 100vh;
			padding: 20px;
			box-sizing: border-box
		}
		.left{
			width: 480px;
			min-width: 320px;
			background: white;
			border-radius: 12px;
			padding: 16px;
			box-shadow: 0 6px 20px rgba(7,9,25,0.06);
			display: flex;
			flex-direction: column
		}
		.right{
			flex: 1;
			background: white;
			border-radius: 12px;
			padding: 16px;
			box-shadow: 0 6px 20px rgba(7,9,25,0.06);
			display: flex;
			flex-direction: column
		}
		h1{
			font-size: 22px;
			margin: 0 0 12px
		}
		.controls{
			display: flex;
			gap: 8px;
			align-items: center;
			margin-bottom: 12px
		}
		button{
			font-size:  15px;
			background: #1f6feb;
			color: white;
			border: 0;
			padding: 8px 12px;
			border-radius: 8px;
			cursor: pointer;
			font-weight: 600
		}
		.list{
			font-size:  16px;
			flex: 1;
			overflow: auto;
			padding: 6px;
			border-radius: 8px
		}
		.item{
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 10px;
			border-radius: 8px;
			border: 1px solid rgba(7,9,25,0.04);
			margin-bottom: 8px;
			background: rgba(255,255,255,0.6);
			cursor: grab
		}
		.item.playing{
			background: rgba(200,200,255,0.6)
		}
		.handle{
			width: 28px;
			height: 28px;
			border-radius: 6px;
			display: flex;
			align-items: center;
			justify-content: center
		}
		.title{
			flex: 1;
			font-weight: 600
		}
		.actions{
			display: flex;
			gap: 8px;
			align-items: center
		}
		.score{
			min-width: 44px;
			text-align: center;
			padding: 6px 8px;
			border-radius: 8px;
			font-weight: 700
		}
		.player-wrap{
			display: flex;
			flex-direction: column;
			gap: 12px
		}
		.player-box{
			aspect-ratio: 16/9;
			background: #000;
			border-radius: 8px;
			overflow: hidden;
			display: flex;
			align-items: center;
			justify-content: center
		}
		iframe{
			width: 100%;
			height: 100%;
			border: 0
		}
		.empty-player{
			color: #c4c9d6
		}
		.meta{
			display: flex;
			gap: 12px;
			align-items: center
		}
		.copynote{
			font-size: 15px;
			color: #506176
		}
		.sortable-drag{
			opacity:  0 !important;
		}
		footer{
			margin-top: auto;
			padding-top: 16px;
			font-size: 15px;
			color: #8893a2
		}
		@media(max-width: 900px){
			.app{
				flex-direction: column
			}
			.left{
				width: 100%;
				min-width: unset;
				height: 360px
			}
			.right{
				width: 100%;
				min-width: unset;
				height: auto
			}
		}

	</style>
	<!-- SortableJS (drag & drop) -->
	<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
</head>
<body>
	<div class="app">
		<section class="left" aria-label="Llista de cançons">
			<h1>Llista de cançons</h1>
			<div class="controls">
				<div class="copynote">Prem per a escoltar-les • Arrossega per a reordenar-les</div>
			</div>

			<div class="list" id="songList" aria-live="polite"></div>

			<footer>
				<button id="copyBtn">Copia els vots al porta-retalls</button>
			</footer>
		</section>

		<section class="right" aria-label="Reproductor YouTube">
			<h1>Reproductor</h1>
			<div class="player-wrap">
				<div class="player-box" id="playerBox">
					<div class="empty-player">Prem una cançó per a escoltar-la.</div>
				</div>
				<div class="meta">
					<div id="currentTitle" style="font-weight:700"></div>
					<div style="flex:1"></div>
					<button id="stopBtn">Atura</button>
				</div>
			</div>
		</section>
	</div>

	<script>
		const SONGS = [
<?php
$error = FALSE;

if (empty($_GET['data'])) {
	$error = TRUE;
} else {
	$decoded = base64_decode($_GET['data']);
	if (!$decoded) {
		$error = TRUE;
	} else {
		$songs = json_decode($decoded, TRUE);
		if (!$songs) {
			$error = TRUE;
		} else {
			shuffle($songs);
		}
	}
}

if ($error) {
	echo "{title: 'S’ha produït un error o no hi ha cap cançó', id: 'dQw4w9WgXcQ'}"."\n";
} else {
	for ($i=0; $i<count($songs);$i++) {
		$title = json_encode($songs[$i]['t']);
		$id = json_encode($songs[$i]['v']);
		echo "{title: ".$title.", id: ".$id."}".($i==(count($songs)-1) ? '' : ',')."\n";
	}
?>

<?php
}
?>
		];
		
		const listEl = document.getElementById('songList');
		const playerBox = document.getElementById('playerBox');
		const currentTitle = document.getElementById('currentTitle');
		const copyBtn = document.getElementById('copyBtn');
		const resetBtn = document.getElementById('resetBtn');
		const stopBtn = document.getElementById('stopBtn');

		function renderList(items){
			listEl.innerHTML = '';
			const n = items.length;
			items.forEach((s, idx) =>{
				const item = document.createElement('div');
				item.className = 'item';
				item.dataset.videoId = s.id;
				item.dataset.title = s.title;

				const handle = document.createElement('div');
				handle.className = 'handle';
				handle.title = 'Arrossega per a reordenar';
				handle.innerHTML = '&#x2630;';

				const title = document.createElement('div');
				title.className = 'title';
				title.textContent = s.title;

				const score = document.createElement('div');
				score.className = 'score';
				score.textContent = (n - idx) + ' punt'+((n - idx)>1 ? 's' : '');
				score.setAttribute('aria-label','Punts: '+(n-idx));

				const actions = document.createElement('div');
				actions.className = 'actions';
				item.addEventListener('click', ()=>{playVideo(s.id, s.title); removePlaying(); item.classList.add('playing');});

				item.appendChild(handle);
				item.appendChild(title);
				item.appendChild(actions);
				item.appendChild(score);

				listEl.appendChild(item);
			});
		}

		let currentOrder = SONGS.slice();
		renderList(currentOrder);

		const sortable = Sortable.create(listEl, {
			scroll: true,
			handle: '.item',
			delay: 250,
			delayOnTouchOnly: true,
			animation: 150,
			onChange: updateScoresAfterDrag,
			onEnd: updateScoresAfterDrag
		});

		function updateScoresAfterDrag(){
			const items = Array.from(listEl.children).filter(child => !child.classList.contains("sortable-fallback"));
			const n = items.length;
			items.forEach((it, idx)=>{
				const sc = it.querySelector('.score');
				sc.textContent = (n - idx) + ' punt'+((n - idx)>1 ? 's' : '');
				sc.setAttribute('aria-label','Punts: '+(n-idx));
			});
			currentOrder = items.map(it=>({title: it.dataset.title, id: it.dataset.videoId}));
		}

		function playVideo(videoId, title){
			playerBox.innerHTML = '';
			const iframe = document.createElement('iframe');
			iframe.src = `https://www.youtube.com/embed/${videoId}?rel=0&autoplay=1`;
			iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
			iframe.allowFullscreen = true;
			playerBox.appendChild(iframe);
			currentTitle.textContent = "Ara escoltes: "+title;
		}
		
		function removePlaying(){
			const items = Array.from(listEl.children);
			const n = items.length;
			items.forEach((it, idx)=>{
				it.classList.remove('playing');
			});
		}

		stopBtn.addEventListener('click', ()=>{
			playerBox.innerHTML = '<div class="empty-player">Prem una cançó per a escoltar-la.</div>';
			currentTitle.textContent = '';
			removePlaying();
		});

		copyBtn.addEventListener('click', async ()=>{
			const items = Array.from(listEl.children);
			const n = items.length;
			const lines = items.map((it, idx)=>{
				const title = it.dataset.title;
				const points = n - idx;
				return `${points}: ${title}`;
			});
			const text = "Aquests són els meus vots:\n\n"+lines.join('\n');
			try{
				await navigator.clipboard.writeText(text);
				copyBtn.textContent = 'Copiat! Ara envia-ho a l’organitzador per MP!';
				copyBtn.style.backgroundColor = 'green';
				setTimeout(()=>{copyBtn.textContent = 'Copia els vots al porta-retalls'; copyBtn.style.backgroundColor = '';},5000);
			}catch(e){
				//Fallback
				window.prompt('Copia manualment el text següent i envia-ho per missatge privat a l’organitzador:', text);
			}
		});

		updateScoresAfterDrag();
	</script>
</body>
</html>
