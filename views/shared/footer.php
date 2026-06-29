    <footer class="footer">
        <p>&copy; <?= date('Y') ?> <?= View::escape($owner ?? 'Portfolio') ?> · <a href="<?= View::escape($github ?? '#') ?>" target="_blank" rel="noreferrer">GitHub</a></p>
    </footer>
    <script src="<?= View::escape($basePath ?? '') ?>assets/js/main.js"></script>
</body>
</html>
