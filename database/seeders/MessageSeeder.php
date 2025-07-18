<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;


class MessageSeeder extends Seeder
{
    public function run()
    {
        $instructors = User::where('role', 'instructor')->get();
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            // Each student sends 0-3 messages to random instructors
            $messageCount = rand(0, 3);
            $recipients = $instructors->random($messageCount);

            foreach ($recipients as $recipient) {
                Message::create([
                    'sender_id' => $student->id,
                    'recipient_id' => $recipient->id,
                    'message' => $this->getStudentMessage(),
                    'is_read' => rand(0, 1),
                    'read_at' => rand(0, 1) ? now()->subDays(rand(0, 7)) : null,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }

            // Each instructor sends 0-2 messages to random students
            $instructorMessageCount = rand(0, 2);
            $studentRecipients = $students->where('id', '!=', $student->id)->random($instructorMessageCount);

            foreach ($studentRecipients as $studentRecipient) {
                Message::create([
                    'sender_id' => $instructors->random()->id,
                    'recipient_id' => $studentRecipient->id,
                    'message' => $this->getInstructorMessage(),
                    'is_read' => rand(0, 1),
                    'read_at' => rand(0, 1) ? now()->subDays(rand(0, 7)) : null,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }

    private function getStudentMessage()
    {
        $messages = [
            "Hi, I have a question about the course material from Lesson 3. Could you clarify the concept of...",
            "I'm having trouble with the assignment. Could you provide some additional guidance?",
            "Thank you for the detailed feedback on my project! It was very helpful.",
            "I wanted to let you know how much I'm enjoying the course. The content is exactly what I was looking for.",
        ];
        return $messages[array_rand($messages)];
    }

    private function getInstructorMessage()
    {
        $messages = [
            "Hi there, I noticed you haven't completed the last assignment yet. Do you need any help?",
            "I've reviewed your project submission and left detailed feedback. Great work overall!",
            "Just checking in to see if you have any questions about the course material.",
            "Congratulations on completing the course! Would you be interested in providing a testimonial?",
        ];
        return $messages[array_rand($messages)];
    }
}
