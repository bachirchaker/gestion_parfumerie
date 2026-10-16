            </div> <!-- End content-container -->
            
            <footer>
                &copy; <?php echo date("Y"); ?> Maison Élégance. Crafted for luxury retail.
            </footer>
        </main> <!-- End main-content -->
    </div> <!-- End wrapper -->
    
    <!-- Script pour animer les compteurs -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll('.animate-counter');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const duration = 1500;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.innerText = Math.ceil(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            
            if(target > 0) updateCounter();
        });
    });
    </script>
</body>
</html>
