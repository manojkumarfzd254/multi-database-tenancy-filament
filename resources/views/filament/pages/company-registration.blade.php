<x-filament-panels::page.simple>
    <form wire:submit="create">
        {{ $this->form }}
        <br>
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const mainElement = document.querySelector("main");
            if (mainElement) {
                mainElement.classList.remove("max-w-lg");
                mainElement.classList.add("max-w-5xl");
            }
        });
    </script>
</x-filament-panels::page.simple>
