import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import {Doughnut} from "react-chartjs-2"

ChartJS.register(ArcElement, Tooltip, Legend);

export default function projectAssigneesDonutChart ({project}) {

    let data = {
        labels: [],
        datasets: [
            {
                label: 'Project Assignees',
                data: [],
                backgroundColor: [
                    'rgba(247, 103, 103, 0.8)',
                    'rgba(71, 129, 255, 0.8)',
                    'rgba(95, 226, 112, 0.8)',
                    'rgba(242, 236, 82, 0.8)',
                    'rgba(255, 174, 68, 0.8)',
                    'rgba(82, 244, 249, 0.8)',
                    'rgba(245, 112, 255, 0.8)',
                    'rgba(212, 80, 252, 0.8)',
                    'rgba(192, 249, 92, 0.8)',
                    'rgba(255, 145, 61, 0.8)'
                ],
                borderColor: [
                    'rgba(247, 103, 103, 1)',
                    'rgba(71, 129, 255, 1)',
                    'rgba(95, 226, 112, 1)',
                    'rgba(242, 236, 82, 1)',
                    'rgba(255, 174, 68, 1)',
                    'rgba(82, 244, 249, 1)',
                    'rgba(245, 112, 255, 1)',
                    'rgba(212, 80, 252, 1)',
                    'rgba(192, 249, 92, 1)',
                    'rgba(255, 145, 61, 1)'
                ],
                borderWidth: 1,
            },
        ],
    };

    project.users.map((user, index) => {
        if (user.tasks_count < 1) {
            return true
        }

        data.labels.push(user.full_name);

        data.datasets[0].data.push(user.tasks_count);
    })

    return (
        <>
            <h2 className={'text-center text-2xl'}>{project.name} Assignees</h2>
            {data.labels.length && <Doughnut data={data}></Doughnut>}
        </>
    );
}
