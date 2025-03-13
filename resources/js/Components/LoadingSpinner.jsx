export default function LoadingSpinner ({className}) {
    return (
        <>
            <div className={"w-6 h-6 inline-block rounded-full border-sky-600 border-t border-b border-l-0 border-r-0 animate-spin " + className}>

            </div>
        </>
    );
}
